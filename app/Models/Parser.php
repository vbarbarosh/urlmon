<?php

namespace App\Models;

use App\Helpers\Traits\Cast;
use App\Helpers\Traits\Q;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property $id
 * @property $uid
 * @property $engine
 * @property $label
 * @property $match
 * @property $config
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property EloquentCollection|Url[] $urls
 * @property $urls_count
 */
class Parser extends Model
{
    use HasFactory, Cast, Q;

    const ENGINE_HTTP_STATUS = 'http_status';
    const ENGINE_HTTP_HEAD = 'http_head';
    const ENGINE_PUPPETEER = 'puppeteer'; // puppeteer/pages puppeteer/meta
    const ENGINE_WGET = 'wget'; // wget/pages wget/meta

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    static public function frontend_list($query): Collection
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

    static public function frontend_fetch($query): Collection
    {
        $c = ['urls'];
        return $query->withCount($c)->get()->map(function (Parser $parser) {
            return [
                'uid' => $parser->uid,
                'label' => $parser->label,
                'engine' => $parser->engine,
                'match' => $parser->match,
                'config' => json_encode($parser->config),
                'total_urls' => $parser->urls_count,
                'created_at' => $parser->created_at->toAtomString(),
                'updated_at' => $parser->updated_at->toAtomString(),
            ];
        });
    }

    static public function store($items)
    {
        $values = [];

        /** @var Parser $item */
        foreach ($items as $item) {
            $values[] = [
                'id' => $item->id,
                'uid' => $item->uid,
                'engine' => $item->engine,
                'label' => $item->label,
                'match' => $item->match,
                'config' => $item->config,
            ];
        }

        Parser::query()->upsert($values, ['id', 'uid'], ['label', 'engine', 'match', 'config']);
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
        $this->label = 'New Parser';
        $this->engine = Parser::ENGINE_HTTP_STATUS;
        $this->match = '.*';
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_parser();
        return $out;
    }

    public function fill_unsafe($input)
    {
        if (isset($input['label'])) {
            $this->label = trim($input['label']) ?: 'New Parser';
        }
        if (isset($input['engine'])) {
            switch ($input['engine']) {
            case Parser::ENGINE_HTTP_STATUS:
            case Parser::ENGINE_HTTP_HEAD:
            case Parser::ENGINE_PUPPETEER:
            case Parser::ENGINE_WGET:
                $this->engine = $input['engine'];
                break;
            }
        }
        if (isset($input['match'])) {
            $this->match = trim($input['match']) ?: '.*';
        }
        if (isset($input['config'])) {
            if (json_decode($input['config'], true) !== null) {
                $this->config = $input['config'];
            }
        }
    }

    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    public function run(Url $url)
    {
        tempdir(function ($d) use ($url) {
            shell([base_path('bin/url-meta'), $url->url, $this->config['js'] ?? ''], $d);
            $url->meta = json_decode(file_get_contents("$d/a.json"), true);
            $url->save();
        });
    }
}
