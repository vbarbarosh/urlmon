<?php

namespace App\Exceptions;

class S3BatchDownloadFailed extends S3Failed
{
    public function __construct(array $errors)
    {
        parent::__construct("S3BatchDownloadFailed\n", $errors);
    }
}
