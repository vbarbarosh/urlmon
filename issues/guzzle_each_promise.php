<?php

require_once __DIR__.'/../vendor/autoload.php';

// https://github.com/aws/aws-sdk-php/issues/1273
// https://github.com/aws/aws-sdk-php/issues/1274
// https://github.com/guzzle/guzzle/issues/1407
// https://github.com/GoogleCloudPlatform/php-docker/issues/468

// $ docker run --rm -v $PWD:/app:ro gcr.io/google-appengine/php70 php /app/issues/s3_command_pool.php
// consumed memory: 1.90G
//
// $ docker run --rm -v $PWD:/app:ro gcr.io/google-appengine/php71 php /app/issues/s3_command_pool.php
// consumed memory: 1.93G
//
// $ docker run --rm -v $PWD:/app:ro gcr.io/google-appengine/php72 php /app/issues/s3_command_pool.php
// consumed memory: 1.93G
//
// $ docker run --rm -v $PWD:/app:ro php:7.0 php /app/issues/s3_command_pool.php
// consumed memory: 104.78M
//
// $ docker run --rm -v $PWD:/app:ro php:7.1 php /app/issues/s3_command_pool.php
// consumed memory: 99.77M
//
// $ docker run --rm -v $PWD:/app:ro php:7.2 php /app/issues/s3_command_pool.php
// consumed memory: 101.03M
//
// $ docker run --rm -v $PWD:/app:ro php:7.3 php /app/issues/s3_command_pool.php
// consumed memory: 101.79M

$mem1 = meminfo();

$client = new \GuzzleHttp\Client();
$pool = function () use ($client) {
    foreach (range(1, 100) as $tmp) {
        yield $client->getAsync('https://httpbin.org/get');
    }
};
(new GuzzleHttp\Promise\EachPromise($pool(), ['concurrency' => 100]))->promise()->wait();

$mem2 = meminfo();
echo sprintf("consumed memory: %s\n", format_bytes($mem1['free'] - $mem2['free']));

function meminfo()
{
    preg_match('/^MemFree:\s*(\d+)/m', file_get_contents('/proc/meminfo'), $free);
    preg_match('/^MemAvailable:\s*(\d+)/m', file_get_contents('/proc/meminfo'), $available);
    $free = $free[1]*1024;
    $available = $available[1]*1024;
    return compact('free', 'available');
}

function format_bytes($bytes)
{
    if ($bytes < 1000) {
        return number_format($bytes, 2);
    }
    $kilo = $bytes/1024;
    if ($kilo < 1000) {
        return number_format($kilo, 2) . 'K';
    }
    $mega = $kilo/1024;
    if ($mega < 1000) {
        return number_format($mega, 2) . 'M';
    }
    $giga = $mega/1024;
    if ($giga < 1000) {
        return number_format($giga, 2) . 'G';
    }
    return number_format($giga/1024, 2) . 'T';
}
