<?php

\vbarbarosh\laravel_debug_eval_longrun([
    'chunk' => 1,
    'init' => function () {
        return \App\Models\Target::query()->pluck('id');
    },
    'done' => function () {
        dump('done');
    },
    'run' => function ($ids) {
        /** @var \App\Models\Target $url */
        foreach (\App\Models\Target::query()->whereIn('id', $ids)->get() as $url) {
            $url->parse();
        }
    },
]);
