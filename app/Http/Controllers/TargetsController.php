<?php

namespace App\Http\Controllers;

use App\Models\Target;
use Illuminate\Http\Request;

class TargetsController extends Controller
{
    /**
     * GET /api/v1/targets.json
     */
    public function list()
    {
        $query = Target::query();
        return Target::frontend_list($query);
    }

    /**
     * GET /api/v1/targets/{target_uid}
     */
    public function fetch($target_uid)
    {
        $target = Target::cast(Target::query()->where('targets.uid', $target_uid)->firstOrFail());
        return Target::frontend_fetch($target->q())->first();
    }

    /**
     * POST /api/v1/targets
     */
    public function create(Request $request)
    {
        $target = new Target();
        $target->fill_unsafe($request->input('target'));
        Target::store([$target]);
        return Target::frontend_fetch($target->q());
    }

    /**
     * PATCH /api/v1/targets/{target_uid}
     */
    public function patch($target_uid, Request $request)
    {
        $target = Target::cast(Target::query()->where('targets.uid', $target_uid)->firstOrFail());
        $target->fill_unsafe($request->input('target'));
        Target::store([$target]);
    }

    /**
     * DELETE /api/v1/targets/{target_uid}
     */
    public function remove($target_uid)
    {
        $target = Target::cast(Target::query()->where('targets.uid', $target_uid)->firstOrFail());
        return Target::remove($target->q());
    }

    /**
     * POST /api/v1/targets/{target_uid}/parse
     */
    public function parse($target_uid)
    {
        $target = Target::cast(Target::query()->where('targets.uid', $target_uid)->firstOrFail());
        $target->parse();
    }
}
