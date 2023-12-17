<?php

use Visus\Cuid2\Cuid2;

function cuid2(int $length = 24): string
{
    $cuid2 = new Cuid2($length);
    return $cuid2->toString();
}

function uid_artifact(): string { return 'art_' . cuid2(); }
function uid_parser(): string { return 'parser_' . cuid2(); }
function uid_promise(): string { return 'prom_' . cuid2(); }
function uid_target(): string { return 'target_' . cuid2(); }
function uid_target_version(): string { return 'ver_' . cuid2(); }
function uid_user(): string { return 'usr_' . cuid2(); }
