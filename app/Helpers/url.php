<?php

use App\Exceptions\UserFriendlyException;
use Illuminate\Support\Facades\Cache;

/**
 * Set, change, or remove query string parameters
 *
 * urlmod('', ['a' => 1])           ?a=1                (set)
 * urlmod('?a=1', ['a' => 2])       ?a=2                (changed)
 * urlmod('?a=1', ['a' => null])                        (remove)
 *
 * @param $url
 * @param array $params
 * @return string
 */
function urlmod($url, array $params): string
{
    $i = strpos($url, '?');
    if ($i === false) {
        $p = $url;
        $q = [];
    }
    else {
        $p = substr($url, 0, $i);
        parse_str(substr($url, $i + 1), $q);
    }
    $q = http_build_query(array_merge($q, $params));
    return $q ? "$p?$q" : $p;
}

function image_url_exists($url)
{
    if (!is_string($url) || !preg_match('!^http://|^https://!', $url)) {
        return false;
    }
    try {
        curl_head($url);
        return true;
    }
    catch (Throwable $exception) {
        return false;
    }
}

function image_extension_from_url($url)
{
    // https://images.unsplash.com/photo-1522165078649-823cf4dbaf46?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjM1MDk4fQ&s=e16bcfbfb85083d6979333fcb365d759
    // https://xxx.xxx.com/ng2j4MJwq0Q8bx1zL2ye7pDGELB91Av5CzemWl5CoL/NDyGe2pK0qns7Xzd.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjM1MDk4fQ&s=95795a954949ffd4bfbf967c1f7b1014
    $extension = strrchr(parse_url($url, PHP_URL_PATH), '.');
    if (!in_array($extension, ['.jpg', '.png', '.gif', '.svg'])) {
        // curl failed: curl_errno=0, http_status=301
        //
        // cli:
        // curl -I 'https://www.dvhn.nl/incoming/cu2ncl-Gemeentehuis-Stadskanaal.jpeg/alternates/LANDSCAPE_960/Gemeentehuis%20Stadskanaal.jpeg'
        //
        // response:
        // HTTP/1.1 301 Moved Permanently
        // location: https://dvhn.nl/incoming/cu2ncl-Gemeentehuis-Stadskanaal.jpeg/alternates/LANDSCAPE_960/Gemeentehuis%20Stadskanaal.jpeg
        // date: Tue, 24 May 2022 18:07:18 GMT
        // server: istio-envoy
        // transfer-encoding: chunked
        $extension = extension_from_mime(curl_head_assoc($url, [CURLOPT_FOLLOWLOCATION => true])['content-type'] ?? 'image/jpeg');
    }
    return $extension;
}

function image_extension_from_url_nothrow($url, $default_extension = '.png')
{
    $out = $default_extension;
    try {
        $out = image_extension_from_url($url);
    }
    catch (Exception $exception) {
        sentry()->captureException($exception);
    }
    return $out;
}

function image_extension_from_pathname($pathname)
{
    $extension = strrchr($pathname, '.');
    if (in_array($extension, ['.jpg', '.png', '.gif', '.svg'])) {
        return $extension;
    }
    throw new UserFriendlyException('Cannot extract image extension from pathname');
}

function image_extension_from_mime($mime)
{
    $extension = extension_from_mime($mime);
    if (in_array($extension, ['.jpg', '.png', '.gif', '.svg'])) {
        return $extension;
    }
    throw new UserFriendlyException('Invalid mime type');
}

function content_length_from_url($url)
{
    return intval(curl_head_assoc($url)['content-length']);
}

function content_length_from_url_cached($url)
{
    $key = "content_length_from_url_cached:$url";
    if (!Cache::has($key)) {
        Cache::put($key, intval(curl_head_assoc($url)['content-length']), now()->addMonth());
    }
    return Cache::get($key);
}
