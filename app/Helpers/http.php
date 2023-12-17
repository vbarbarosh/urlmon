<?php

function http_get_contents($url, $offset = null, $length = null)
{
    $context = stream_context_create([
        'http' => [
            // // https://github.com/php/php-src/commit/f9f769d4b9af367af864d35cf09dca5b08da2046
            // // file_get_contents(https://www.dvhn.nl/incoming/cknn73-20190531DX6033.jpg/alternates/LANDSCAPE_960/20190531DX6033.jpg): failed to open stream: HTTP request failed! HTTP/1.1 426 Upgrade Required
            // $url = 'https://www.dvhn.nl/incoming/cknn73-20190531DX6033.jpg/alternates/LANDSCAPE_960/20190531DX6033.jpg';
            // $ctx = stream_context_create(['http' => ['protocol_version' => '1.1']]);
            // dump(strlen(file_get_contents($url, false, $ctx)));
            'protocol_version' => '1.1',
            'header' => "x-urlmon: true\r\n",
        ]
    ]);
    if (isset($length)) {
        return file_get_contents($url, false, $context, $offset, $length);
    }
    if (isset($offset)) {
        return file_get_contents($url, false, $context, $offset);
    }
    return file_get_contents($url, false, $context);
}
