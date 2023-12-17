<?php

namespace App\Exceptions;

class S3BatchUploadFailed extends S3Failed
{
    public function __construct(array $errors)
    {
        parent::__construct("S3BatchUploadFailed\n", $errors);
    }
}
