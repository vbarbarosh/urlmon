<?php

namespace App\Exceptions;

class S3BatchACLFailed extends S3Failed
{
    public function __construct(array $errors)
    {
        parent::__construct("S3BatchACLFailed\n", $errors);
    }
}
