<?php

namespace App\Models;

use App\Exceptions\UserFriendlyException;
use App\Helpers\Classes\FrontendArray;
use App\Helpers\Traits\Cast;
use App\Helpers\Traits\Q;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Alternative names: urls, resources, documents, targets
 *
 * @property $id
 * @property $uid
 * @property $parser_id
 * @property $label
 * @property $url
 * @property $meta
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Parser $parser
 * @property EloquentCollection|Artifact[] $artifacts
 * @property TargetVersion $latest
 * @property TargetVersion|TargetVersion[] $versions
 * @property $artifacts_count
 */
class Target extends Model
{
    use HasFactory, Cast, Q;

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    static public function frontend_list($query): Collection
    {
        $r = ['parser'];
        $c = ['artifacts'];
        $targets = $query->with($r)->withCount($c)->get();
        $artifacts = Artifact::query()->whereIn('type', Artifact::TYPE_IMAGES)->whereIn('target_id', $targets->pluck('id'))->get()->groupBy('target_id');
        $expires_at = now()->addHour();
        return $targets->map(function (Target $target) use ($artifacts, $expires_at) {
            return new FrontendArray($target->id, [
                'uid' => $target->uid,
                'parser_uid' => $target->parser->uid ?? null,
                'label' => $target->label,
                'url' => $target->url,
                'images' => empty($artifacts[$target->id]) ? [] : $artifacts[$target->id]->map(function (Artifact $artifact) use ($expires_at) {
                    return ['url' => s3_sign_get($artifact->url, $expires_at), 'width' => $artifact->meta['width'], 'height' => $artifact->meta['height']];
                }),
                'artifacts_count' => $target->artifacts_count,
                'created_at' => $target->created_at->toAtomString(),
                'updated_at' => $target->updated_at->toAtomString(),
            ]);
        });
    }

    static public function frontend_fetch($query): Collection
    {
        $r = ['parser'];
        $c = ['artifacts'];
        $targets = $query->with($r)->withCount($c)->get();
        $artifacts = Artifact::frontend_fetch(Artifact::query()->whereIn('target_id', $targets->pluck('id')))->keyBy('id');
        return $targets->map(function (Target $target) use ($artifacts) {
            return new FrontendArray($target->id, [
                'uid' => $target->uid,
                'parser_uid' => $target->parser->uid ?? null,
                'label' => $target->label,
                'url' => $target->url,
                'meta' => $target->meta,
                'artifacts_count' => $target->artifacts_count,
                'artifacts' => $artifacts->where('target_id', $target->id)->values(),
                'created_at' => $target->created_at->toAtomString(),
                'updated_at' => $target->updated_at->toAtomString(),
            ]);
        });
    }

    static public function store($items)
    {
        $values = [];

        /** @var Target $item */
        foreach ($items as $item) {
            $values[] = [
                'id' => $item->id,
                'uid' => $item->uid,
                'parser_id' => $item->parser_id,
                'label' => $item->label,
                'url' => $item->url,
            ];
        }

        Target::query()->upsert($values, ['id', 'uid'], ['parser_id', 'label', 'url']);
    }

    /**
     * @throws Exception
     */
    static public function remove($query): int
    {
        safety_check_query_for_batch_remove($query);

        return $query->pluck('targets.id')->chunk(100)->sum(function ($ids) {
            Artifact::remove(Artifact::query()->whereIn('target_id', $ids));
            return Target::query()->whereIn('id', $ids)->delete();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->uid = uid_target();
        $this->parser_id = 1;
        $this->url = 'https://example.com/' . $this->uid;
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_target();
        return $out;
    }

    public function parser()
    {
        return $this->belongsTo(Parser::class);
    }

    public function latest()
    {
        return $this->belongsTo(TargetVersion::class, 'latest_version_id');
    }

    public function versions()
    {
        return $this->hasMany(TargetVersion::class);
    }

    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }

    public function fill_unsafe($input)
    {
        if (isset($input['parser_uid'])) {
            /** @var Parser $parser */
            $parser = Parser::query()->where('uid', $input['parser_uid'])->first();
            if (!$parser) {
                throw new UserFriendlyException("Parser not found: {$input['parser_uid']}");
            }
            $this->parser_id = $parser->id;
        }
        if (isset($input['label'])) {
            $this->label = trim($input['label']);
        }
        if (isset($input['url'])) {
            $this->url = trim($input['url']);
        }
    }

    public function parse()
    {
        $this->parser->run($this);
    }

    public function attach($file, $name = null)
    {
        $name = $name ?? basename($file);
        $artifact = new Artifact();
        $artifact->target_id = $this->id;
        $artifact->name = $name;
        $artifact->url = s3_files_url(sprintf('%s/%s', $artifact->uid, $artifact->name));
        $artifact->size = filesize($file);
        $artifact->type = mime_from_pathname($file);
        $tmp = getimagesize($file);
        if ($tmp) {
            $artifact->meta = [
                'width' => $tmp[0],
                'height' => $tmp[1],
            ];
        }
        s3_put_object($artifact->url, ['SourceFile' => $file, 'ACL' => S3_ACL_PRIVATE]);
        $artifact->save();
    }

    public function attach_body($name, $body)
    {
        $artifact = new Artifact();
        $artifact->target_id = $this->id;
        $artifact->name = $name;
        $artifact->url = s3_files_url(sprintf('%s/%s', $artifact->uid, $artifact->name));
        $artifact->size = strlen($body);
        $artifact->type = mime_from_pathname($name);
        s3_put_object($artifact->url, ['Body' => $body, 'ACL' => S3_ACL_PRIVATE]);
        $artifact->save();
    }
}
