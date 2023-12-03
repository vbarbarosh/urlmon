<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;

class ArtifactsController extends Controller
{
    /**
     * GET /api/v1/artifacts.json
     */
    public function list(Request $request)
    {
        $q = filters($request->input(), Artifact::query(), [
            'target' => function (EloquentBuilder $q, $value) {
                $q->whereRaw('(artifacts.target_id = (SELECT id FROM targets WHERE targets.uid = ? LIMIT 1))', [$value]);
            },
        ]);
        return Artifact::frontend_list($q);
    }

    /**
     * GET /api/v1/artifacts/{artifact_uid}
     */
    public function fetch($artifact_uid)
    {
        $artifact = Artifact::cast(Artifact::query()->where('artifacts.uid', $artifact_uid)->firstOrFail());
        return Artifact::frontend_fetch($artifact->q())->first();
    }

    /**
     * DELETE /api/v1/artifacts/{artifact_uid}
     */
    public function remove($artifact_uid)
    {
        $artifact = Artifact::cast(Artifact::query()->where('artifacts.uid', $artifact_uid)->firstOrFail());
        Artifact::remove($artifact->q());
    }
}
