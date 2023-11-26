<?php

namespace App\Http\Controllers;

use App\Models\Parser;
use Illuminate\Http\Request;

class ParsersController extends Controller
{
    /**
     * GET /api/v1/parsers.json
     */
    public function list()
    {
        return Parser::frontend_list(Parser::query());
    }

    /**
     * GET /api/v1/parsers/{parser_uid}
     */
    public function fetch($parser_uid)
    {
        /** @var Parser $parser */
        $parser = Parser::query()->where('parsers.uid', $parser_uid)->firstOrFail();
        return Parser::frontend_fetch(Parser::query()->where('id', $parser->id))->first();
    }

    public function patch($parser_uid, Request $request)
    {
    }

    /**
     * DELETE /api/v1/parsers/{parser_uid}
     */
    public function remove($parser_uid)
    {
        /** @var Parser $parser */
        $parser = Parser::query()->where('parsers.uid', $parser_uid)->firstOrFail();
        Parser::remove(Parser::query()->where('id', $parser->id));
    }
}
