<?php

/**
 * Deterministic version of json_encode so you can a
 * result a unique message in Redis queue.
 *
 * @param $value
 * @return string
 */
function json_stringify_stable($value)
{
//    // The following will change order or plain arrays. Only objects should be sorted.
//    if (false) {
//        if (is_array($value)) {
//            array_multisort($value);
//        }
//    }
    return json_stringify($value);
}

function json_stringify($value)
{
    return json_encode($value, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}

function json_stringify_pretty($value)
{
    return json_encode($value, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}

function json_stringify_pretty_keep_empty_objects($value)
{
    return json_encode($value, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);
}
