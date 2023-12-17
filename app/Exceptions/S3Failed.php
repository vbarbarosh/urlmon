<?php

namespace App\Exceptions;

use Aws\Exception\AwsException;
use Exception;
use Throwable;

class S3Failed extends Exception
{
    public $errors;

    public function __construct($prefix, array $errors)
    {
        $tmp = [];
        foreach ($errors as $row) {
            $error = $row['error'];
            if ($error instanceof AwsException) {
                $tmp[] = sprintf('[%s] %s: %s', $error->getAwsErrorCode(), $error->getAwsErrorMessage(), json_stringify($row['input']));
            }
            else if ($error instanceof Throwable) {
                $tmp[] = sprintf('[%s] %s: %s', $error->getCode(), $error->getMessage(), json_stringify($row['input']));
            }
            else {
                $tmp[] = sprintf('[%s] %s: %s', $error->getCode(), $error->getMessage(), json_stringify($row['input']));
            }
        }
        parent::__construct($prefix . implode("\n", $tmp));
        $this->errors = $errors;
    }
}
