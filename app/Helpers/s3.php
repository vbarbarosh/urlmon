<?php

use Aws\Credentials\Credentials;
use Aws\Result as AwsResult;
use Aws\S3\S3Client;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Stream as GuzzleStream;

define('S3_CACHE_10MIN', 'public, max-age=600, s-maxage=600');
define('S3_REVALIDATE', 'public, max-age=0, s-maxage=1200');
define('S3_MEDIA', 'public, max-age=' . (365*24*60*60));
define('S3_NOCACHE', 'private, max-age=0, no-cache');

define('S3_ACL_PUBLIC_READ', 'public-read');
define('S3_ACL_PRIVATE', 'private');

function group_urls(array $urls): array
{
    static $s3_clients = null;

    if ($s3_clients === null) {
        $s3_clients = [];
        $s3_clients[s3_files_url()] = s3_files_client();
    }

    $custom_urls = [];
    $unknown_urls = [];

    $unique_urls = [];
    foreach ($urls as $url) {
        if (empty($url) || isset($unique_urls[$url])) {
            continue;
        }
        $unique_urls[$url] = $url;
    }

    foreach ($unique_urls as $url) {
        foreach ($s3_clients as $prefix => $s3_client) {
            if (str_starts_with($url, $prefix)) {
                $custom_urls[$prefix][] = $url;
                continue 2;
            }
        }
        $unknown_urls[] = $url;
    }

    $out = [];

    // Put unknown urls first, so that exception might be thrown if they are no allowed
    $out[] = ['client' => null, 'urls' => $unknown_urls];

    foreach ($custom_urls as $prefix => $urls) {
        $out[] = ['client' => $s3_clients[$prefix], 'urls' => $urls];
    }

    return $out;
}

function s3_parse(string $url): array
{
    $tmp = parse_url($url);
    return [
        'Bucket' => empty($tmp['host']) ? null : $tmp['host'],
        'Key' => empty($tmp['path']) ? null : substr($tmp['path'], 1),
    ];
}

function s3_sign_get($url, Carbon $expires_at, array $options = []): string
{
    /** @var S3Client $s3_client */
    $s3_client = group_urls([$url])[1]['client'];
    $cmd = $s3_client->getCommand('getObject', array_merge(s3_parse($url), $options));
    return (string) $s3_client->createPresignedRequest($cmd, $expires_at)->getUri();
}

function s3_sign_get_nothrow(string $url, Carbon $expires_at, array $options = []): ?string
{
    $groups = group_urls([$url]);
    if (empty($groups[1])) {
        return null;
    }

    /** @var S3Client $s3_client */
    $s3_client = group_urls([$url])[1]['client'];
    $cmd = $s3_client->getCommand('getObject', array_merge(s3_parse($url), $options));
    return (string) $s3_client->createPresignedRequest($cmd, $expires_at)->getUri();
}

function s3_sign_get_domain($url, Carbon $expires_at, array $options = [])
{
    /** @var S3Client[] */
    static $client_from_bucket = [];

    $params = array_merge(s3_parse($url), $options);

    if (empty($client_from_bucket[$params['Bucket']])) {
        /** @var S3Client $s3_client */
        $s3_client = group_urls([$url])[1]['client'];
        /** @var Credentials $credentials */
        $credentials = $s3_client->getCredentials()->wait();
        $client_from_bucket[$params['Bucket']] = new S3Client([
            'credentials' => [
                'key' => $credentials->getAccessKeyId(),
                'secret' => $credentials->getSecretKey(),
            ],
            'region' => $s3_client->getRegion(),
            'version' => '2006-03-01',
            //'endpoint' => "http://{$params['Bucket']}",
            'endpoint' => "https://127.0.0.1:9001",
            //'bucket_endpoint' => true,
            'bucket_endpoint' => false,
            'use_path_style_endpoint' => true,
        ]);
    }

    $s3_client = $client_from_bucket[$params['Bucket']];
    $cmd = $s3_client->getCommand('getObject', $params);

    // copy of \Aws\S3\S3Client::createPresignedRequest
    $cmd->getHandlerList()->remove('signer');

    /** @var \Aws\Signature\SignatureInterface $signer */
    $signer = call_user_func(
        $s3_client->getSignatureProvider(),
        $s3_client->getConfig('signature_version'),
        $s3_client->getConfig('signing_name'),
        $s3_client->getConfig('signing_region')
    );

    $request = \Aws\serialize($cmd);
    $request = $request->withUri($request->getUri()->withQuery(strval(parse_url($url, PHP_URL_QUERY))));
    $s = (string) $signer->presign($request, $s3_client->getCredentials()->wait(), $expires_at)->getUri();
    return str_replace('https://127.0.0.1:9001', 'https://', $s);
}

/**
 * Usage:
 *
 *     $url = s3_files_url('test/hello.txt');
 *     $put = s3_sign_put($url, now()->addMinutes(10), ['ACL' => S3_ACL_PUBLIC_READ, 'Content-Type' => 'text/plain']);
 *     tempdir(function ($d) use ($put) {
 *         file_put_contents("$d/a.txt", sprintf("hello %s\n", now()->toAtomString()));
 *         return shell(['curl', '-sfS', $put, '-T', 'a.txt', '-H', 'Content-Type:text/plain'], $d);
 *     });
 *     return curl_head($url);
 *
 * Bugs:
 *     https://github.com/aws/aws-sdk-php/issues/2609
 *     After upgrading from aws/aws-sdk-php:3.222 to aws/aws-sdk-php:3.255 all new signed urls become broken.
 *
 * @param string $url
 * @param Carbon $expires_at
 * @param array $options
 * @return string
 * @link https://docs.aws.amazon.com/AmazonS3/latest/dev/RetrieveObjSingleOpPHP.html
 */
function s3_sign_put(string $url, Carbon $expires_at, array $options = []): string
{
    /** @var S3Client $s3_client */
    $s3_client = group_urls([$url])[1]['client'];
    $cmd = $s3_client->getCommand('putObject', array_merge(s3_parse($url), $options));
    return (string) $s3_client->createPresignedRequest($cmd, $expires_at)->getUri();
}

function s3_files_client(): S3Client
{
    static $inst;
    if ($inst === null) {
        // http://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.S3.S3Client.html
        $inst = new S3Client([
            'credentials' => [
                'key' => env('S3_KEY'),
                'secret' => env('S3_SECRET'),
            ],
            'version' => '2006-03-01',
            'region' => env('S3_REGION'),
            'endpoint' => env('S3_ENDPOINT'),
            'bucket_endpoint' => false,
            'use_path_style_endpoint' => true,
        ]);
    }
    return $inst;
}

function s3_files_bucket(): string
{
    return env('S3_BUCKET');
}

function s3_files_url(string $key = ''): string
{
    return sprintf('https://%s/%s', s3_files_bucket(), ltrim($key, '/'));
}

function s3_client(string $url): S3Client
{
    $tmp = group_urls([$url]);
    if (count($tmp[0]['urls'])) {
        throw new Error("Could not resolve s3 client for $url");
    }
    return $tmp[1]['client'];
}

// s3_put($url, ['Body' => $body])
// s3_put($url, ['SourceFile' => $zip->pathname])
function s3_put_object(string $url, array $options = []): AwsResult
{
    $type = ['ContentType' => mime_from_pathname($url)];
    return s3_client($url)->putObject(array_merge(s3_parse($url), $type, $options));
}

// s3_get($url)->get('Body')
function s3_get_object(string $url, array $options = []): AwsResult
{
    return s3_client($url)->getObject(array_merge(s3_parse($url), $options));
}

// s3_get_contents($url)
function s3_get_contents(string $url, array $options = []): string
{
    /** @var GuzzleStream $stream */
    $stream = s3_get_object($url, $options)->get('Body');
    return $stream->getContents();
}

// s3_list('https://minio.test/tmp/')
function s3_list(string $prefix, array $options = []): AwsResult
{
    $tmp = s3_parse($prefix);
    $tmp['Prefix'] = $tmp['Key'];
    unset($tmp['Key']);
    return s3_client($prefix)->listObjects(array_merge($tmp, $options));
}

function s3_list_urls(string $prefix, int $limit = 50000, array $options = []): array
{
    $tmp = s3_parse($prefix);
    $tmp['Prefix'] = $tmp['Key'];
    unset($tmp['Key']);

    $out = [];
    $Marker = '';
    for ($i = 0; $i < 100; ++$i) {
        $result = s3_client($prefix)->listObjects(array_merge($tmp, $options, ['Marker' => $Marker]));
        foreach ((array) $result->get('Contents') as $item) {
            $out[] = sprintf('https://%s/%s', $tmp['Bucket'], $item['Key']);
            if (count($out) >= $limit) {
                return $out;
            }
            $Marker = $item['Key'];
        }
        if (!$result->get('IsTruncated')) {
            break;
        }
    }
    return $out;
}

function s3_rm(string $url, array $options = []): AwsResult
{
    return s3_client($url)->deleteObject(array_merge(s3_parse($url), $options));
}
