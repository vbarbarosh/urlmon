<?php

use App\Exceptions\CurlFailed;

function curl_format_cli(array $options): string
{
    $args = [];

    // Authentication methods
    if (isset($options[CURLOPT_HTTPAUTH])) {
        switch ($options[CURLOPT_HTTPAUTH]) {
        case CURLAUTH_BASIC:
            $args[] = '--basic';
            break;
        case CURLAUTH_DIGEST:
            $args[] = '--digest';
            break;
        case CURLAUTH_GSSNEGOTIATE:
            $args[] = '--negotiate';
            break;
        case CURLAUTH_NTLM:
            $args[] = '--ntlm';
            break;
        case CURLAUTH_ANY:
            $args[] = '--anyauth';
            break;
        case CURLAUTH_ANYSAFE:
            // XXX Curl does not support this option
            $args[] = '--anyauth-safe';
            break;
        default:
            throw new Exception("Invalid value: {$options[CURLOPT_HTTPAUTH]}");
        }
    }

    // Username and password
    if (isset($options[CURLOPT_USERPWD])) {
        $args[] = '-u';
        $args[] = shell_arg($options[CURLOPT_USERPWD]);
    }

    // Request methods
    if (isset($options[CURLOPT_CUSTOMREQUEST])) {
        $args[] = '-X';
        $args[] = $options[CURLOPT_CUSTOMREQUEST];
    }
    else if (isset($options[CURLOPT_HEADER]) && $options[CURLOPT_HEADER]) {
        $args[] = '-I';
    }

    // Url
    $args[] = shell_arg($options[CURLOPT_URL]);

    // Headers
    if (isset($options[CURLOPT_HTTPHEADER])) {
        foreach ($options[CURLOPT_HTTPHEADER] as $value) {
            $args[] = '-H';
            $args[] = shell_arg(trim($value));
        }
    }

    if (isset($options[CURLOPT_XOAUTH2_BEARER])) {
        $args[] = '-H';
        $args[] = shell_arg('Authorization: Bearer ' . trim($options[CURLOPT_XOAUTH2_BEARER]));
    }

    // Form data
    if (isset($options[CURLOPT_POST]) && $options[CURLOPT_POST]) {
        $json = false;
        if (isset($options[CURLOPT_HTTPHEADER])) {
            foreach ($options[CURLOPT_HTTPHEADER] as $value) {
                if (preg_match('/\s*content-type:/i', $value)) {
                    $json = true;
                    break;
                }
            }
        }
        if ($json) {
            $args[] = '-d';
            $args[] = shell_arg($options[CURLOPT_POSTFIELDS]);
        }
        else {
            parse_str($options[CURLOPT_POSTFIELDS], $query);
            foreach ($query as $key => $value) {
                $args[] = '-d';
                $args[] = shell_arg("$key=$value");
            }
        }
    }

    // Timeout
    if (isset($options[CURLOPT_TIMEOUT])) {
        $args[] = '-m';
        $args[] = floatval($options[CURLOPT_TIMEOUT]);
    }

    return 'curl ' . implode(' ', $args);
}

function curl_translate_options(array $options): array
{
    static $str = null;
    if ($str === null) {
        $str = [];
        /** @noinspection SpellCheckingInspection */
        foreach (explode(':', 'CURLOPT_ACCEPTTIMEOUT_MS:CURLOPT_ACCEPT_ENCODING:CURLOPT_ADDRESS_SCOPE:CURLOPT_APPEND:CURLOPT_AUTOREFERER:CURLOPT_BINARYTRANSFER:CURLOPT_BUFFERSIZE:CURLOPT_CAINFO:CURLOPT_CAPATH:CURLOPT_CERTINFO:CURLOPT_CLOSEPOLICY:CURLOPT_CONNECTTIMEOUT:CURLOPT_CONNECTTIMEOUT_MS:CURLOPT_CONNECT_ONLY:CURLOPT_CONNECT_TO:CURLOPT_COOKIE:CURLOPT_COOKIEFILE:CURLOPT_COOKIEJAR:CURLOPT_COOKIELIST:CURLOPT_COOKIESESSION:CURLOPT_CRLF:CURLOPT_CRLFILE:CURLOPT_CUSTOMREQUEST:CURLOPT_DEFAULT_PROTOCOL:CURLOPT_DIRLISTONLY:CURLOPT_DNS_CACHE_TIMEOUT:CURLOPT_DNS_INTERFACE:CURLOPT_DNS_LOCAL_IP4:CURLOPT_DNS_LOCAL_IP6:CURLOPT_DNS_SERVERS:CURLOPT_DNS_USE_GLOBAL_CACHE:CURLOPT_EGDSOCKET:CURLOPT_ENCODING:CURLOPT_EXPECT_100_TIMEOUT_MS:CURLOPT_FAILONERROR:CURLOPT_FILE:CURLOPT_FILETIME:CURLOPT_FNMATCH_FUNCTION:CURLOPT_FOLLOWLOCATION:CURLOPT_FORBID_REUSE:CURLOPT_FRESH_CONNECT:CURLOPT_FTPAPPEND:CURLOPT_FTPASCII:CURLOPT_FTPLISTONLY:CURLOPT_FTPPORT:CURLOPT_FTPSSLAUTH:CURLOPT_FTP_ACCOUNT:CURLOPT_FTP_ALTERNATIVE_TO_USER:CURLOPT_FTP_CREATE_MISSING_DIRS:CURLOPT_FTP_FILEMETHOD:CURLOPT_FTP_RESPONSE_TIMEOUT:CURLOPT_FTP_SKIP_PASV_IP:CURLOPT_FTP_SSL:CURLOPT_FTP_SSL_CCC:CURLOPT_FTP_USE_EPRT:CURLOPT_FTP_USE_EPSV:CURLOPT_FTP_USE_PRET:CURLOPT_GSSAPI_DELEGATION:CURLOPT_HEADER:CURLOPT_HEADERFUNCTION:CURLOPT_HEADEROPT:CURLOPT_HTTP200ALIASES:CURLOPT_HTTPAUTH:CURLOPT_HTTPGET:CURLOPT_HTTPHEADER:CURLOPT_HTTPPROXYTUNNEL:CURLOPT_HTTP_CONTENT_DECODING:CURLOPT_HTTP_TRANSFER_DECODING:CURLOPT_HTTP_VERSION:CURLOPT_IGNORE_CONTENT_LENGTH:CURLOPT_INFILE:CURLOPT_INFILESIZE:CURLOPT_INTERFACE:CURLOPT_IPRESOLVE:CURLOPT_ISSUERCERT:CURLOPT_KEYPASSWD:CURLOPT_KRB4LEVEL:CURLOPT_KRBLEVEL:CURLOPT_LOCALPORT:CURLOPT_LOCALPORTRANGE:CURLOPT_LOGIN_OPTIONS:CURLOPT_LOW_SPEED_LIMIT:CURLOPT_LOW_SPEED_TIME:CURLOPT_MAIL_AUTH:CURLOPT_MAIL_FROM:CURLOPT_MAIL_RCPT:CURLOPT_MAXCONNECTS:CURLOPT_MAXFILESIZE:CURLOPT_MAXREDIRS:CURLOPT_MAX_RECV_SPEED_LARGE:CURLOPT_MAX_SEND_SPEED_LARGE:CURLOPT_MUTE:CURLOPT_NETRC:CURLOPT_NETRC_FILE:CURLOPT_NEW_DIRECTORY_PERMS:CURLOPT_NEW_FILE_PERMS:CURLOPT_NOBODY:CURLOPT_NOPROGRESS:CURLOPT_NOPROXY:CURLOPT_NOSIGNAL:CURLOPT_PASSWDFUNCTION:CURLOPT_PASSWORD:CURLOPT_PATH_AS_IS:CURLOPT_PINNEDPUBLICKEY:CURLOPT_PIPEWAIT:CURLOPT_PORT:CURLOPT_POST:CURLOPT_POSTFIELDS:CURLOPT_POSTQUOTE:CURLOPT_POSTREDIR:CURLOPT_PREQUOTE:CURLOPT_PRIVATE:CURLOPT_PROGRESSFUNCTION:CURLOPT_PROTOCOLS:CURLOPT_PROXY:CURLOPT_PROXYAUTH:CURLOPT_PROXYHEADER:CURLOPT_PROXYPASSWORD:CURLOPT_PROXYPORT:CURLOPT_PROXYTYPE:CURLOPT_PROXYUSERNAME:CURLOPT_PROXYUSERPWD:CURLOPT_PROXY_SERVICE_NAME:CURLOPT_PROXY_TRANSFER_MODE:CURLOPT_PUT:CURLOPT_QUOTE:CURLOPT_RANDOM_FILE:CURLOPT_RANGE:CURLOPT_READDATA:CURLOPT_READFUNCTION:CURLOPT_REDIR_PROTOCOLS:CURLOPT_REFERER:CURLOPT_RESOLVE:CURLOPT_RESUME_FROM:CURLOPT_RETURNTRANSFER:CURLOPT_RTSP_CLIENT_CSEQ:CURLOPT_RTSP_REQUEST:CURLOPT_RTSP_SERVER_CSEQ:CURLOPT_RTSP_SESSION_ID:CURLOPT_RTSP_STREAM_URI:CURLOPT_RTSP_TRANSPORT:CURLOPT_SAFE_UPLOAD:CURLOPT_SASL_IR:CURLOPT_SERVICE_NAME:CURLOPT_SHARE:CURLOPT_SOCKS5_GSSAPI_NEC:CURLOPT_SOCKS5_GSSAPI_SERVICE:CURLOPT_SSH_AUTH_TYPES:CURLOPT_SSH_HOST_PUBLIC_KEY_MD5:CURLOPT_SSH_KNOWNHOSTS:CURLOPT_SSH_PRIVATE_KEYFILE:CURLOPT_SSH_PUBLIC_KEYFILE:CURLOPT_SSLCERT:CURLOPT_SSLCERTPASSWD:CURLOPT_SSLCERTTYPE:CURLOPT_SSLENGINE:CURLOPT_SSLENGINE_DEFAULT:CURLOPT_SSLKEY:CURLOPT_SSLKEYPASSWD:CURLOPT_SSLKEYTYPE:CURLOPT_SSLVERSION:CURLOPT_SSL_CIPHER_LIST:CURLOPT_SSL_ENABLE_ALPN:CURLOPT_SSL_ENABLE_NPN:CURLOPT_SSL_FALSESTART:CURLOPT_SSL_OPTIONS:CURLOPT_SSL_SESSIONID_CACHE:CURLOPT_SSL_VERIFYHOST:CURLOPT_SSL_VERIFYPEER:CURLOPT_SSL_VERIFYSTATUS:CURLOPT_STDERR:CURLOPT_STREAM_WEIGHT:CURLOPT_TCP_FASTOPEN:CURLOPT_TCP_KEEPALIVE:CURLOPT_TCP_KEEPIDLE:CURLOPT_TCP_KEEPINTVL:CURLOPT_TCP_NODELAY:CURLOPT_TELNETOPTIONS:CURLOPT_TFTP_BLKSIZE:CURLOPT_TFTP_NO_OPTIONS:CURLOPT_TIMECONDITION:CURLOPT_TIMEOUT:CURLOPT_TIMEOUT_MS:CURLOPT_TIMEVALUE:CURLOPT_TLSAUTH_PASSWORD:CURLOPT_TLSAUTH_TYPE:CURLOPT_TLSAUTH_USERNAME:CURLOPT_TRANSFERTEXT:CURLOPT_TRANSFER_ENCODING:CURLOPT_UNIX_SOCKET_PATH:CURLOPT_UNRESTRICTED_AUTH:CURLOPT_UPLOAD:CURLOPT_URL:CURLOPT_USERAGENT:CURLOPT_USERNAME:CURLOPT_USERPWD:CURLOPT_USE_SSL:CURLOPT_VERBOSE:CURLOPT_WILDCARDMATCH:CURLOPT_WRITEFUNCTION:CURLOPT_WRITEHEADER:CURLOPT_XOAUTH2_BEARER') as $key) {
            if (defined($key)) {
                $str[constant($key)] = $key;
            }
        }
    }

    $out = [];
    foreach ($options as $key => $value) {
        $out[isset($str[$key]) ? $str[$key] : $key] = $value;
    }
    return $out;
}

function curl_parse_headers(string $headers): array
{
    $out = [];
    foreach (explode("\r\n", $headers) as $index => $s) {
        if ($index == 0) {
            $out['STATUS'] = $s;
        }
        else {
            $p = strpos($s, ':');
            $out[strtolower(substr($s, 0, $p))] = trim(substr($s, $p + 1));
        }
    }
    return $out;
}

// https://stackoverflow.com/a/14436877/1478566
function curl_verbose_begin(): array
{
    return [
        CURLOPT_VERBOSE => true,
        CURLOPT_STDERR => fopen('php://temp', 'rw+'),
    ];
}

function curl_verbose_end(array $options): string
{
    $fp = $options[CURLOPT_STDERR];
    rewind($fp);
    $out = stream_get_contents($fp);
    fclose($fp);
    return $out;
}

/**
 * This is a little bit out-of-system way to fight with the APIs
 * that does not follow standard HTTP way to report about errors
 * (to return 400+ status code to indicate an error). The goal
 * of this function is to fail the whole curl request if the
 * response body indicates error.
 *
 * @param array $options
 * @return mixed
 * @throws CurlFailed
 */
function curl_exec_throw(array $options): mixed
{
    if (isset($options['transform_response'])) {
        $transform_response = $options['transform_response'];
        unset($options['transform_response']);
    }
    else {
        $transform_response = function ($body, /** @noinspection PhpUnusedParameterInspection */ array $details) {
            return $body;
        };
    }

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $http_body = curl_exec($curl);
    $curl_errno = curl_errno($curl);
    $curl_info = curl_getinfo($curl);
    curl_close($curl);
    $details = compact('options', 'curl_errno', 'curl_info', 'http_body');
    // https://api.adform.com/v1/help/bannermanagement/banners#!/ThirdPartyBanners/ThirdPartyBanners_Post
    //
    // POST /api/v1/ThirdPartyBanners HTTP/1.1
    // Accept: application/json, */*
    // Accept-Encoding: gzip, deflate
    // Authorization: Bearer [...]
    // Connection: keep-alive
    // Content-Length: 369
    // Content-Type: application/json
    // Host: api.adform.com
    // User-Agent: HTTPie/0.9.9
    //
    // {
    //     "CampaignId": 1437974,
    //     "ClickUrl": {
    //         "Url": "http://example.com"
    //     },
    //     "CommonSettings": {
    //         "FormatId": 0
    //     },
    //     "Content": {
    //         "ExpandableSettings": {
    //             "ExpandedSize": {
    //                 "Height": 300,
    //                 "Width": 400
    //             },
    //             "ExpansionDirection": 1
    //         },
    //         "Script": {
    //             "Code": "<html><head><title>Hello</title></head><body><p>Hello</p></body></html>"
    //         },
    //         "Size": {
    //             "Height": 300,
    //             "Width": 400
    //         },
    //         "ThirdPartyBannerType": 0
    //     },
    //     "Title": "hello"
    // }
    //
    // HTTP/1.1 202 Accepted
    // Connection: keep-alive
    // Content-Length: 213
    // Content-Type: application/json; charset=utf-8
    // CorrelationId: 640d2ea7-c5cf-45a3-a207-d53dda0bc107
    // Date: Thu, 13 Sep 2018 12:47:38 GMT
    // RequestId: 380ea35b-edb0-40df-99b3-978187cd8464
    // Server: Zulus Microsoft-HTTPAPI/2.0
    //
    // {
    //     "Errors": null,
    //     "Value": "640d2ea7-c5cf-45a3-a207-d53dda0bc107",
    //     "Warnings": null,
    //     "_links": [
    //         {
    //             "description": "CheckOperationResult",
    //             "href": "api/v1/OperationResults/640d2ea7-c5cf-45a3-a207-d53dda0bc107",
    //             "method": "GET"
    //         }
    //     ]
    // }
    //
    // ------------------------------------------------------------
    //
    // GET /api/v1/OperationResults/caa814d1-f0ef-47ff-b7de-1e273456b1c0 HTTP/1.1
    // Accept-Encoding: gzip, deflate
    // Host: api.adform.com
    // Accept: */*
    // User-Agent: HTTPie/0.9.9
    // Connection: keep-alive
    // Authorization: Bearer [...]
    //
    //
    //
    //
    // HTTP/1.1 204 No Content
    // Date: Thu, 13 Sep 2018 13:12:25 GMT
    // Content-Length: 0
    // Connection: keep-alive
    // Server: Zulus Microsoft-HTTPAPI/2.0
    // RequestId: 61bef81c-98d6-4d8c-a36f-522becb2af25
    // CorrelationId: 3c30941a-6c72-4ad5-9e4f-f292e5f813ba
    if ($curl_errno == 0 && ($curl_info['http_code'] >= 200 && $curl_info['http_code'] < 300)) {
        return $transform_response($http_body, $details);
    }
    throw new CurlFailed($details);
}

/**
 * @throws CurlFailed
 */
function curl_head(string $url, array $options = []): string
{
    $params = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
    ];
    return curl_exec_throw(array_merge_int_keys($params, $options));
}

/**
 * @throws CurlFailed
 */
function curl_head_assoc(string $url, array $options = []): array
{
    return curl_parse_headers(curl_head($url, $options));
}

/**
 * @throws CurlFailed
 */
function curl_get(string $url, array $options = []): mixed
{
    $params = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
    ];
    return curl_exec_throw(array_merge_int_keys($params, $options));
}

/**
 * @throws CurlFailed
 */
function curl_get_json(string $url, array $options = []): mixed
{
    $params = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
    ];
    $params = array_merge_int_keys($params, $options);
    $params['transform_response'] = function ($body, array $details) use ($options) {
        $json = json_decode($body, true);
        return isset($options['transform_response']) ? $options['transform_response']($json, $details) : $json;
    };
    return curl_exec_throw($params);
}

/**
 * @throws CurlFailed
 */
function curl_post_urlencoded(string $url, array $post_fields = [], array $options = []): mixed
{
    $params = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($post_fields),
        CURLOPT_RETURNTRANSFER => true,
    ];
    return curl_exec_throw(array_merge_int_keys($params, $options));
}

/**
 * @throws CurlFailed
 */
function curl_post_json(string $url, array $post_fields = [], array $options = []): mixed
{
    $params = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_stringify($post_fields),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
    ];
    $params = array_merge_int_keys($params, $options);
    $params['transform_response'] = function ($body, array $details) use ($options) {
        $json = json_decode($body, true);
        return isset($options['transform_response']) ? $options['transform_response']($json, $details) : $json;
    };
    return curl_exec_throw($params);
}

/**
 * @throws CurlFailed
 */
function curl_put_json(string $url, array $post_fields = [], array $options = []): mixed
{
    return curl_post_json($url, $post_fields, array_merge_int_keys([CURLOPT_CUSTOMREQUEST => 'PUT'], $options));
}

/**
 * @throws CurlFailed
 * @link https://stackoverflow.com/a/7348741
 */
function curl_put_file(string $url, string $file, array $options = []): mixed
{
    clearstatcache(true, $file);

    $fp = fopen($file, 'r');
    try {
        $params = [
            CURLOPT_URL => $url,
            CURLOPT_PUT => true,
            CURLOPT_INFILE => $fp,
            CURLOPT_INFILESIZE => filesize($file),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: ' . mime_from_pathname(parse_url($url, PHP_URL_PATH))],
        ];
        return curl_exec_throw(array_merge_int_keys($params, $options));
    }
    finally {
        fclose($fp);
    }
}

/**
 * @throws CurlFailed
 */
function curl_put_utf8(string $url, string $utf8, array $options = []): mixed
{
    $params = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $utf8,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: text/plain; charset=UTF-8'],
    ];
    return curl_exec_throw(array_merge_int_keys($params, $options));
}
