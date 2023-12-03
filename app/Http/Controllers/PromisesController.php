<?php

namespace App\Http\Controllers;

use App\Models\Promise;

class PromisesController extends Controller
{
    /**
     * GET /api/v1/promises.json
     */
    public function list()
    {
        return Promise::frontend_list(Promise::query());
    }

    /**
     * GET /api/v1/promises/{promise_uid}
     */
    public function fetch($promise_uid)
    {
        $promise = Promise::cast(Promise::query()->where('promises.uid', $promise_uid)->firstOrFail());
        return Promise::frontend_fetch($promise->q())->first();
    }

    /**
     * DELETE /api/v1/Promises/{Promise_uid}
     */
    public function remove($promise_uid)
    {
        $promise = Promise::cast(Promise::query()->where('promises.uid', $promise_uid)->firstOrFail());
        Promise::remove($promise->q());
    }
}
