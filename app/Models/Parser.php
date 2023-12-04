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
 * @property EloquentCollection|Target[] $targets
 * @property $targets_count
 */
class Parser extends Model
{
    use HasFactory, Cast, Q;

    const ENGINE_HTTP_STATUS = 'http_status';
    const ENGINE_HTTP_HEAD = 'http_head';
    const ENGINE_PUPPETEER_META = 'puppeteer/meta';
    const ENGINE_PUPPETEER_PAGES = 'puppeteer/pages';
    const ENGINE_WGET_META = 'wget/meta';
    const ENGINE_WGET_PAGES = 'wget/pages';

    protected $hidden = [
        'id',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    static public function frontend_list($query): Collection
    {
        $c = ['targets'];
        return $query->withCount($c)->get()->map(function (Parser $parser) {
            return [
                'uid' => $parser->uid,
                'label' => $parser->label,
                'engine' => $parser->engine,
                // 'match' => $parser->match,
                // 'config' => $parser->config,
                'targets_count' => $parser->targets_count,
                'created_at' => $parser->created_at->toAtomString(),
                'updated_at' => $parser->updated_at->toAtomString(),
            ];
        });
    }

    static public function frontend_fetch($query): Collection
    {
        $c = ['targets'];
        return $query->withCount($c)->get()->map(function (Parser $parser) {
            return [
                'uid' => $parser->uid,
                'label' => $parser->label,
                'engine' => $parser->engine,
                'match' => $parser->match,
                'config' => json_encode($parser->config),
                'targets_count' => $parser->targets_count,
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
            Target::remove(Target::query()->whereIn('parser_id', $ids));
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
            case Parser::ENGINE_PUPPETEER_META:
            case Parser::ENGINE_PUPPETEER_PAGES:
            case Parser::ENGINE_WGET_META:
            case Parser::ENGINE_WGET_PAGES:
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

    public function targets()
    {
        return $this->hasMany(Target::class);
    }

    public function run(Target $url)
    {
        switch ($this->engine) {
        case Parser::ENGINE_HTTP_STATUS:
            $this->run_http_status($url);
            break;
        case Parser::ENGINE_HTTP_HEAD:
            $this->run_http_head($url);
            break;
        case Parser::ENGINE_PUPPETEER_META:
            $this->run_puppeteer($url);
            break;
        }
    }

    private function run_http_status(Target $target)
    {
        $s = shell(['curl', '-is', $target->url]);
        $s = explode("\r\n", $s)[0];
        $s = explode("\r\n", $s)[0];
        $s = trim($s);
        $target->meta = $s;
        $target->save();
        $this->create_dummy_artifacts($target);
    }

    private function run_http_head(Target $target)
    {
        $s = shell(['curl', '-isI', $target->url]);
        $s = explode("\r\n\r\n", $s)[0];
        $target->meta = $s;
        $target->save();
        $this->create_dummy_artifacts($target);
    }

    private function run_puppeteer(Target $target)
    {
        tempdir(function ($d) use ($target) {
            shell(['timeout', '30s', base_path('bin/url-meta'), $target->url, $this->config['js'] ?? ''], $d);
            $target->meta = json_decode(file_get_contents("$d/a.json"), true);
            $target->save();
            $this->create_dummy_artifacts($target);
            $target->attach("$d/a.png", 'screenshot.png');
        });
    }

    private function create_dummy_artifacts(Target $target): void
    {
        Artifact::remove($target->artifacts());
        $target->attach_body('db.csv', $target->toJson());
        $target->attach_body('logs.txt', $target->toJson());
    }
}
