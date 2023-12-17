<?php

namespace App\Exceptions;

use Exception;

class CurlFailed extends Exception
{
    public $curl_errno;
    public $http_status;
    public $http_body;

    public function __construct(array $details)
    {
        $options = $details['options'];
        $curl_errno = $details['curl_errno'];
        $curl_info = $details['curl_info'];
        $http_body = $details['http_body'];
        $message = empty($details['message']) ? '' : $details['message'];

        $this->curl_errno = $curl_errno;
        $this->http_status = $curl_info['http_code'];
        $this->http_body = $http_body;

        $cli = curl_format_cli($options);
        // $options_str = 'curl_exec_throw(' . var_export(curl_translate_options($options), true) . ')';
        $http_status = $curl_info['http_code'];
        parent::__construct("curl failed: curl_errno=$curl_errno, http_status=$http_status $message\n\ncli:\n$cli\n\nresponse:\n$http_body");
    }
}
