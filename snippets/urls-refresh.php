<?php

\vbarbarosh\laravel_debug_eval_longrun([
    'chunk' => 1,
    'init' => function () {
        return \App\Models\Url::query()->pluck('id');
    },
    'done' => function () {
        dump('done');
    },
    'run' => function ($ids) {
        /** @var \App\Models\Url $url */
        foreach (\App\Models\Url::query()->whereIn('id', $ids)->get() as $url) {
            $url->parse();
        }
    },
]);
