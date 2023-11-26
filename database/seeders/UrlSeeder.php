<?php

namespace Database\Seeders;

use App\Models\Parser;
use App\Models\Url;

class UrlSeeder
{
    public function run()
    {
        /** @var Parser $parser */
        $parser = Parser::query()->where('match', 'https://news.ycombinator.com/item?id=\d+')->firstOrFail();

        $url = new Url();
        $url->url = 'https://news.ycombinator.com/item?id=38423469';
        $url->parser_id = $parser->id;
        $url->save();

        $url = new Url();
        $url->url = 'https://news.ycombinator.com/item?id=38424939';
        $url->parser_id = $parser->id;
        $url->save();
    }
}
