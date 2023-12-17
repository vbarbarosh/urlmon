<?php

/**
 * Standard `array_merge` resets integer keys. This version will keep them:
 *
 *     array_merge([1 => 11, 2 => 22], [4 => 44, 5 => 55])
 *     [0 => 11, 1 => 22, 2 => 44, 3 => 55]
 *
 *     array_merge_int_keys([1 => 11, 2 => 22], [4 => 44, 5 => 55])
 *     [1 => 11, 2 => 22, 4 => 44, 5 => 55]
 *
 * @param array $out
 * @param mixed ...$other
 * @return mixed
 */
function array_merge_int_keys(array $out, ...$other): array
{
    foreach ($other as $second) {
        foreach ($second as $key => $value) {
            $out[$key] = $value;
        }
    }
    return $out;
}
