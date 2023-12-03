<?php

namespace App\Http\Controllers;

use App\Models\Artifact;

class ArtifactsController extends Controller
{
    /**
     * GET /api/v1/artifacts.json
     */
    public function list()
    {
        return Artifact::frontend_list(Artifact::query());
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
