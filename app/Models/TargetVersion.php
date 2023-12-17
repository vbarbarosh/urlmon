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
 * @property $target_id
 * @property $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Target[] $targets
 */
class TargetVersion extends Model
{
    use HasFactory, Cast, Q;

    protected $hidden = [
        'id',
        'target_id',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    static public function frontend_list($query): Collection
    {
        $c = ['targets'];
        return $query->withCount($c)->get()->map(function (TargetVersion $version) {
            return [
                'uid' => $version->uid,
                'value' => $version->value,
                'created_at' => $version->created_at->toAtomString(),
                'updated_at' => $version->updated_at->toAtomString(),
            ];
        });
    }

    static public function frontend_fetch($query): Collection
    {
        return TargetVersion::frontend_list($query);
    }

    static public function store($items)
    {
        $values = [];

        /** @var TargetVersion $item */
        foreach ($items as $item) {
            $values[] = [
                'id' => $item->id,
                'uid' => $item->uid,
                'target_id' => $item->target_id,
                'value' => $item->value,
            ];
        }

        TargetVersion::query()->upsert($values, ['id', 'uid'], ['target_id', 'value']);
    }

    /**
     * @throws Exception
     */
    static public function remove($query): int
    {
        safety_check_query_for_batch_remove($query);

        return $query->pluck('parsers.id')->chunk(100)->sum(function ($ids) {
            Target::remove(Target::query()->whereIn('parser_id', $ids));
            return TargetVersion::query()->whereIn('id', $ids)->delete();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->uid = uid_parser();
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_target_version();
        return $out;
    }

    public function fill_unsafe($input)
    {
        throw new NotImplemented();
    }
}
