<?php

namespace App\Models;

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
 * @property $url
 * @property $meta
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Parser $parser
 */
class Url extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    static public function frontend_list($query): Collection
    {
        return $query->get()->map(function (Url $url) {
            return [
                'uid' => $url->uid,
                'url' => $url->url,
                'meta' => $url->meta,
                'created_at' => $url->created_at->toAtomString(),
                'updated_at' => $url->updated_at->toAtomString(),
            ];
        });
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
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_url();
        return $out;
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
