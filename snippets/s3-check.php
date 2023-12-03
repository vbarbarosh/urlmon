<?php

$url = 'https://minio.test/hello.txt';
s3_put_object($url, ['Body' => '11']);
dump(s3_sign_get_domain($url, now()->addHour()));
