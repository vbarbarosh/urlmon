<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $uid
 * @property $label
 * @property $engine
 * @property $match
 * @property $config
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property EloquentCollection|Url[] $urls
 * @property $urls_count
 */
class Parser extends Model
{
    use HasFactory;

    const ENGINE_PUPPETEER = 'puppeteer';
    const ENGINE_WGET = 'wget';

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    static public function frontend_list($query)
    {
        $c = ['urls'];
        return $query->withCount($c)->get()->map(function (Parser $parser) {
            return [
                'uid' => $parser->uid,
                'label' => $parser->label,
                'engine' => $parser->engine,
                // 'match' => $parser->match,
                // 'config' => $parser->config,
                'total_urls' => $parser->urls_count,
                'created_at' => $parser->created_at->toAtomString(),
                'updated_at' => $parser->updated_at->toAtomString(),
            ];
        });
    }

    /**
     * @throws Exception
     */
    static public function remove($query): int
    {
        safety_check_query_for_batch_remove($query);

        return $query->pluck('parsers.id')->chunk(100)->sum(function ($ids) {
            Url::query()->whereIn('parser_id', $ids)->update(['parser_id' => null]);
            return Parser::query()->whereIn('id', $ids)->delete();
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
        $out->uid = uid_parser();
        return $out;
    }

    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    public function run(Url $url)
    {
        tempdir(function ($d) use ($url) {
            shell([base_path('bin/url-meta'), $url->url, $this->config['js']], $d);
            $url->meta = json_decode(file_get_contents("$d/a.json"), true);
            $url->save();
        });
    }
}
