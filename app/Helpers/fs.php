<?php

function filename($path): string
{
    return pathinfo($path, PATHINFO_FILENAME);
}

function extname($path): string
{
    return pathinfo($path, PATHINFO_EXTENSION);
}

function tempdir(callable $cb): mixed
{
    for ($attempt = 1; $attempt <= 5; ++$attempt) {
        $d = sys_get_temp_dir() . DIRECTORY_SEPARATOR . bin2hex(random_bytes(16));
        if (file_exists($d)) {
            unset($d);
        }
        else {
            mkdir($d);
            break;
        }
    }

    if (!isset($d)) {
        throw new Exception("tempdir: could not create temporary directory after $attempt attempt(s)");
    }

    try {
        return call_user_func($cb, $d);
    }
    finally {
        rmdir_r($d);
    }
}

function rmdir_r(string $pathname): void
{
    if (is_file($pathname)) {
        unlink($pathname);
        return;
    }

    foreach (scandir($pathname) as $file) {
        if ($file != '.' && $file != '..') {
            rmdir_r("$pathname/$file");
        }
    }

    rmdir($pathname);
}

/**
 * @throws Throwable
 */
function mkdirp(string $pathname, int $mode = 0777): string
{
    // Take in mind race conditions
    if (!file_exists($pathname)) {
        try {
            mkdir($pathname, $mode, true);
        }
        catch (Throwable $exception) {
            if (!file_exists($pathname)) {
                throw $exception;
            }
        }
    }
    return $pathname;
}
