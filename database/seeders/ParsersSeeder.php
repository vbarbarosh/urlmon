<?php

namespace Database\Seeders;

use App\Models\Parser;

class ParsersSeeder
{
    public function run(): void
    {
        $parser = new Parser();
        $parser->label = 'news.ycombinator.com/item';
        $parser->match = 'https://news.ycombinator.com/item?id=\d+';
        $parser->engine = Parser::ENGINE_PUPPETEER;
        $parser->config = [
            'js' => <<< EOF
{
    title: document.querySelector('.titleline > a').innerText,
    target_url: document.querySelector('.titleline > a').href,
    score: parseInt(document.querySelector('.score').innerText),
    total_comments: document.querySelectorAll('.comment').length,
    created_at: document.querySelector('.age').title,
}
EOF
        ];
        $parser->save();

        $parser = new Parser();
        $parser->label = 'news.ycombinator.com/user';
        $parser->match = 'https://news.ycombinator.com/user?id=.+';
        $parser->engine = Parser::ENGINE_PUPPETEER;
        $parser->config = [
            'js' => <<< EOF
{
    user: document.querySelector('.hnuser').innerText,
    created_at: new Date(document.querySelector('.athing [timestamp]').attributes.timestamp.value*1000).toJSON(),
    karma: +document.querySelector('.athing ~ tr:nth-child(3) td ~ td').innerText,
    about: document.querySelector('.athing ~ tr:nth-child(4) td ~ td').innerText,
}
EOF
        ];
        $parser->save();

        $parser = new Parser();
        $parser->label = 'news.ycombinator.com/news';
        $parser->match = 'https://news.ycombinator.com(/|/news)?';
        $parser->engine = Parser::ENGINE_PUPPETEER;
        $parser->config = [
            'js' => <<< EOF
{
    pages: [],
    items: ([...document.querySelectorAll('.athing:not(:has(a[rel*=nofollow]))')].map(function (elem) {
        return {
            url: elem.nextSibling.querySelector('.subline > a:last-child').href,
            title: elem.querySelector('.titleline > a').innerText,
            target_url: elem.querySelector('.titleline > a').href,
            user: elem.nextSibling.querySelector('.hnuser').innerText,
            points: parseInt(elem.nextSibling.querySelector('.score').innerText),
            created_at: elem.nextSibling.querySelector('.age').title
        };
    }))
}
EOF
        ];
        $parser->save();
    }
}
