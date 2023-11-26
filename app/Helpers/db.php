<?php

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @param $query
 * @throws Exception
 */
function safety_check_query_for_batch_remove($query): void
{
    if ($query instanceof HasMany || $query instanceof HasManyThrough) {
        if (empty($query->getQuery()->getQuery()->wheres)) {
            throw new Exception("It's suspicious to pass query without WHERE conditions");
        }
        return;
    }
    if ($query instanceof EloquentBuilder) {
        if (empty($query->getQuery()->wheres)) {
            throw new Exception("It's suspicious to pass query without WHERE conditions");
        }
        return;
    }
    throw new Exception('Cannot check if query is safe to be used for removal');
}
