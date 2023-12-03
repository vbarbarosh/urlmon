<?php

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

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

/**
 * @param $filters
 * @param EloquentBuilder|Relation $query
 * @param array $dic
 * @return mixed
 */
function filters($filters, $query, array $dic = [])
{
    $limit = intval($filters['limit'] ?? 100);
    if ($query instanceof Relation) {
        $tmp = $query->getBaseQuery()->limit;
    }
    else {
        $tmp = $query->getQuery()->limit;
    }
    if ($tmp) {
        $limit = min($tmp, $limit);
    }
    $page = intval($filters['page'] ?? 1);
    $offset = $filters['offset'] ?? null;
    $query->limit($limit);
    if ($offset !== null) {
        $query->offset(intval($offset));
    }
    else {
        $query->offset(($page - 1)*$limit);
    }
    foreach ($filters as $name => $value) {
        if ($value === false) {
            continue; // `false` values are those not checked in ui
        }
        if (isset($dic[$name])) {
            call_user_func($dic[$name], $query, $value);
        }
    }
    return $query;
}
