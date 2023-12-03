<?php

namespace App\Models;

use App\Exceptions\UserFriendlyException;
use App\Helpers\Traits\Cast;
use App\Helpers\Traits\Q;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Alternative names: resources, documents
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
 */
class Url extends Model
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
        return $query->with($r)->get()->map(function (Url $url) {
            return [
                'uid' => $url->uid,
                'parser_uid' => $url->parser->uid ?? null,
                'label' => $url->label,
                'url' => $url->url,
                'meta' => $url->meta,
                'created_at' => $url->created_at->toAtomString(),
                'updated_at' => $url->updated_at->toAtomString(),
            ];
        });
    }

    static public function frontend_fetch($query): Collection
    {
        return Url::frontend_list($query);
    }

    static public function store($items)
    {
        $values = [];

        /** @var Url $item */
        foreach ($items as $item) {
            $values[] = [
                'id' => $item->id,
                'uid' => $item->uid,
                'parser_id' => $item->parser_id,
                'label' => $item->label,
                'url' => $item->url,
            ];
        }

        Url::query()->upsert($values, ['id', 'uid'], ['parser_id', 'label', 'url']);
    }

    /**
     * @throws Exception
     */
    static public function remove($query): int
    {
        safety_check_query_for_batch_remove($query);

        return $query->pluck('urls.id')->chunk(100)->sum(function ($ids) {
            return Url::query()->whereIn('id', $ids)->delete();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->uid = uid_url();
        $this->parser_id = 1;
        $this->url = 'https://example.com/' . $this->uid;
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_url();
        return $out;
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

    public function parser()
    {
        return $this->belongsTo(Parser::class);
    }

    public function parse()
    {
        $this->parser->run($this);
    }
}
