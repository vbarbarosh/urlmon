<?php

function mime_from_pathname($pathname)
{
    static $mime_from_ext = [
        '.txt' => 'text/plain',
        '.css' => 'text/css',
        '.html' => 'text/html',
        '.xml' => 'text/xml',
        '.js' => 'text/javascript',
        '.json' => 'application/json',
        '.png' => 'image/png',
        '.gif' => 'image/gif',
        '.svg' => 'image/svg+xml',
        '.jpg' => 'image/jpeg',
        '.jpeg' => 'image/jpeg',

        // https://stackoverflow.com/a/10864297/1478566
        '.ttf' => 'application/x-font-ttf',
        '.otf' => 'application/x-font-opentype',
        '.woff' => 'application/font-woff',
        '.woff2' => 'application/font-woff2',
        '.eot' => 'application/vnd.ms-fontobject',
        '.sfnt' => 'application/font-sfnt',

        '.mp4' => 'video/mp4',
        '.ogg' => 'video/ogg', // https://en.wikipedia.org/wiki/Theora
        '.ogv' => 'video/ogg', // https://en.wikipedia.org/wiki/Theora
        '.webm' => 'video/webm',

        // https://stackoverflow.com/a/4212908/1478566
        '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    return $mime_from_ext[strtolower(strrchr(basename($pathname), '.'))] ?? null;
}

function extension_from_mime($mime)
{
    static $ext_from_mime = [
        'text/plain' => '.txt',
        'text/css' => '.css',
        'text/html' => '.html',
        'text/xml' => '.xml',
        'text/javascript' => '.js',
        'application/json' => '.json',
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/svg+xml' => '.svg',
        'image/jpeg' => '.jpg',
        'image/jpg' => '.jpg',

        'font/ttf' => '.ttf',
        'font/otf' => '.otf',
        // https://stackoverflow.com/a/10864297/1478566
        'application/x-font-ttf' => '.ttf',
        'application/x-font-opentype' => '.otf',
        'application/font-woff' => '.woff',
        'application/font-woff2' => '.woff2',
        'application/vnd.ms-fontobject' => '.eot',
        'application/font-sfnt' => '.sfnt',

        'video/mp4' => '.mp4',
        'video/ogg' => '.ogg',
        'video/webm' => '.webm',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
    ];

    return $ext_from_mime[$mime] ?? null;
}
