<?php

namespace App\Http\Controllers;

use App\Exceptions\NotImplemented;
use App\Models\Url;
use Illuminate\Http\Request;

class UrlsController extends Controller
{
    /**
     * GET /api/v1/urls.json
     */
    public function list()
    {
        $query = Url::query();
        return Url::frontend_list($query);
    }

    /**
     * GET /api/v1/urls/{url_uid}
     */
    public function fetch($url_uid)
    {
        /** @var Url $url */
        $url = Url::query()->where('urls.uid', $url_uid)->firstOrFail();
        return Url::frontend_list(Url::query()->where('id', $url->id))->first();
    }

    /**
     * POST /api/v1/urls
     */
    public function create(Request $request)
    {
        throw new NotImplemented();
    }

    /**
     * POST /api/v1/urls/{url_uid}/parse
     */
    public function parse($url_uid)
    {
        /** @var Url $url */
        $url = Url::query()->where('urls.uid', $url_uid)->firstOrFail();
        $url->parse();
    }

    /**
     * PATCH /api/v1/urls/{url_uid}
     */
    public function patch($url_uid, Request $request)
    {
        /** @var Url $url */
        $url = Url::query()->where('urls.uid', $url_uid)->firstOrFail();
        return Url::frontend_list(Url::query()->where('id', $url->id))->first();
    }

    /**
     * DELETE /api/v1/urls/{url_uid}
     */
    public function remove($url_uid)
    {
        /** @var Url $url */
        $url = Url::query()->where('urls.uid', $url_uid)->firstOrFail();
        return Url::remove(Url::query()->where('id', $url->id));
    }
}
