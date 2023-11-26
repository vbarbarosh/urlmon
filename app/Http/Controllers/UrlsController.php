<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlsController extends Controller
{
    public function list()
    {
        $query = Url::query();
        return Url::frontend_list($query);
    }
}
