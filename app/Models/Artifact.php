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
        $targets = Target::frontend_list(Target::query()->whereIn('id', $target_ids))->keyBy('id');
        return $artifacts->map(function (Artifact $artifact)  use ($targets) {
            return new FrontendArray($artifact->id, [
                'uid' => $artifact->uid,
                'target_uid' => $targets[$artifact->target_id]['uid'] ?? null,
                'target' => $targets[$artifact->target_id] ?? null,
                'name' => $artifact->name,
                'url' => $artifact->url,
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
        $targets = Promise::frontend_fetch(Promise::query()->whereIn('id', $target_ids))->keyBy('id');
        return $artifacts->map(function (Artifact $artifact)  use ($targets) {
            return [
                'uid' => $artifact->uid,
                'target_uid' => $targets[$artifact->target_id]['uid'] ?? 'null',
                'target' => $targets[$artifact->target_id] ?? null,
                'name' => $artifact->name,
                'url' => $artifact->url,
                'size' => $artifact->size,
                'created_at' => $artifact->created_at->toAtomString(),
                'updated_at' => $artifact->updated_at->toAtomString(),
            ];
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
