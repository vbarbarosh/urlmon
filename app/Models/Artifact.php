<?php

namespace App\Models;

use App\Exceptions\NotImplemented;
use App\Helpers\Classes\FrontendArray;
use App\Helpers\Traits\Cast;
use App\Helpers\Traits\Q;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\RateLimiter\AbstractRequestRateLimiter;
use Throwable;

/**
 * @property $id
 * @property $uid
 * @property $target_id
 * @property $name
 * @property $url
 * @property $size
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Target $target
 */
class Artifact extends Model
{
    use HasFactory, Cast, Q;

    protected $hidden = [
        'id',
        'target_id',
    ];

    static public function frontend_list($query): Collection
    {
        $artifacts = $query->get();
        $target_ids = $artifacts->pluck('target_id');
        $targets = Target::frontend_list(Target::query()->whereIn('targets.id', $target_ids))->keyBy('id');
        return $artifacts->map(function (Artifact $artifact)  use ($targets) {
            $ids = ['id' => $artifact->id, 'target_id' => $artifact->target_id];
            return new FrontendArray($ids, [
                'uid' => $artifact->uid,
                'target_uid' => $targets[$artifact->target_id]['uid'] ?? null,
                'name' => $artifact->name,
                'url' => s3_sign_get_nothrow($artifact->url, now()->addHour()),
                'size' => $artifact->size,
                'created_at' => $artifact->created_at->toAtomString(),
                'updated_at' => $artifact->updated_at->toAtomString(),
            ]);
        });
    }

    static public function frontend_fetch($query): Collection
    {
        $artifacts = $query->get();
        $target_ids = $artifacts->pluck('target_id');
        $targets = Target::frontend_list(Target::query()->whereIn('targets.id', $target_ids))->keyBy('id');
        return $artifacts->map(function (Artifact $artifact)  use ($targets) {
            $ids = ['id' => $artifact->id, 'target_id' => $artifact->target_id];
            return new FrontendArray($ids, [
                'uid' => $artifact->uid,
                'target_uid' => $targets[$artifact->target_id]['uid'] ?? null,
                'target' => $targets[$artifact->target_id] ?? null,
                'name' => $artifact->name,
                'url' => s3_sign_get_nothrow($artifact->url, now()->addHour()),
                'size' => $artifact->size,
                'created_at' => $artifact->created_at->toAtomString(),
                'updated_at' => $artifact->updated_at->toAtomString(),
            ]);
        });
    }

    static public function store($items)
    {
        $values = [];

        /** @var Artifact $item */
        foreach ($items as $item) {
            $values[] = [
                'id' => $item->id,
                'target_id' => $item->target_id,
                'uid' => $item->uid,
                'name' => $item->name,
                'url' => $item->url,
                'size' => $item->size,
            ];
        }

        Artifact::query()->upsert($values, ['id', 'uid'], ['target_id', 'name', 'url', 'size']);
    }

    /**
     * @throws Exception
     */
    static public function remove($query): int
    {
        safety_check_query_for_batch_remove($query);

        return $query->pluck('artifacts.id')->chunk(100)->sum(function ($ids) {
            $urls = Artifact::query()->whereIn('id', $ids)->whereNotNull('url')->distinct()->pluck('url');
            foreach ($urls as $url) {
                if (str_starts_with($url, s3_files_url())) {
                    s3_rm($url);
                }
            }
            return Artifact::query()->whereIn('id', $ids)->delete();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->uid = uid_artifact();
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_artifact();
        return $out;
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

    public function fill_unsafe($input)
    {
        throw new NotImplemented();
    }
}
