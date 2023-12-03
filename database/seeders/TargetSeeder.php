<?php

namespace Database\Seeders;

use App\Models\Parser;
use App\Models\Target;

class TargetSeeder
{
    public function run()
    {
        $parser = Parser::cast(Parser::query()->where('engine', Parser::ENGINE_HTTP_STATUS)->firstOrFail());

        $target = new Target();
        $target->parser_id = $parser->id;
        $target->label = 'STATUS example.com';
        $target->url = 'https://example.com';
        $target->save();

        $parser = Parser::cast(Parser::query()->where('engine', Parser::ENGINE_HTTP_HEAD)->firstOrFail());

        $target = new Target();
        $target->parser_id = $parser->id;
        $target->label = 'HEAD example.com';
        $target->url = 'https://example.com';
        $target->save();

        $parser = Parser::cast(Parser::query()->where('match', 'https://news.ycombinator.com/item?id=\d+')->firstOrFail());

        $target = new Target();
        $target->parser_id = $parser->id;
        $target->label = 'NH: Darling: Run macOS Software on Linux (darlinghq.org)';
        $target->url = 'https://news.ycombinator.com/item?id=38423469';
        $target->save();

        $target = new Target();
        $target->parser_id = $parser->id;
        $target->label = 'NH: Understanding Deep Learning (udlbook.github.io)';
        $target->url = 'https://news.ycombinator.com/item?id=38424939';
        $target->save();
    }
}
