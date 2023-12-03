<?php

namespace App\Models;

use App\Exceptions\NotImplemented;
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
 * @property $promise_id
 * @property $name
 * @property $url
 * @property $size
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Promise $promise
 */
class Artifact extends Model
{
    use HasFactory, Cast, Q;

    protected $hidden = [
        'id',
        'promise_id',
    ];

    static public function frontend_list($query): Collection
    {
        $artifacts = $query->get();
        $promise_ids = $artifacts->pluck('promise_id');
        $promises = Promise::frontend_list(Promise::query()->whereIn('id', $promise_ids))->keyBy('id');
        return $artifacts->map(function (Artifact $artifact)  use ($promises) {
            return [
                'uid' => $artifact->uid,
                'promise' => $promises[$artifact->promise_id] ?? null,
                'name' => $artifact->name,
                'url' => $artifact->url,
                'size' => $artifact->size,
                'created_at' => $artifact->created_at->toAtomString(),
                'updated_at' => $artifact->updated_at->toAtomString(),
            ];
        });
    }

    static public function frontend_fetch($query): Collection
    {
        $artifacts = $query->get();
        $promise_ids = $artifacts->pluck('promise_id');
        $promises = Promise::frontend_fetch(Promise::query()->whereIn('id', $promise_ids))->keyBy('id');
        return $artifacts->map(function (Artifact $artifact)  use ($promises) {
            return [
                'uid' => $artifact->uid,
                'promise' => $promises[$artifact->promise_id] ?? null,
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
                'promise_id' => $item->promise_id,
                'uid' => $item->uid,
                'name' => $item->name,
                'url' => $item->url,
                'size' => $item->size,
            ];
        }

        Artifact::query()->upsert($values, ['id', 'uid'], ['promise_id', 'name', 'url', 'size']);
    }

    /**
     * @throws Exception
     */
    static public function remove($query): int
    {
        safety_check_query_for_batch_remove($query);

        return $query->pluck('parsers.id')->chunk(100)->sum(function ($ids) {
            Url::remove(Url::query()->whereIn('parser_id', $ids));
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

    public function promise()
    {
        return $this->belongsTo(Promise::class);
    }

    public function fill_unsafe($input)
    {
        throw new NotImplemented();
    }
}
