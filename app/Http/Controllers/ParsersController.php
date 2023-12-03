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
        return Parser::frontend_fetch($parser->q())->first();
    }

    /**
     * POST /api/v1/parsers
     */
    public function create(Request $request)
    {
        $parser = new Parser();
        $parser->fill_unsafe($request->input('parser'));
        Parser::store([$parser]);
        return Parser::frontend_fetch($parser->q())->first();
    }

    /**
     * PATCH /api/v1/parsers/{parser_uid}
     */
    public function patch($parser_uid, Request $request)
    {
        /** @var Parser $parser */
        $parser = Parser::query()->where('parsers.uid', $parser_uid)->firstOrFail();
        $parser->fill_unsafe($request->input('parser'));
        Parser::store([$parser]);
    }

    /**
     * DELETE /api/v1/parsers/{parser_uid}
     */
    public function remove($parser_uid)
    {
        /** @var Parser $parser */
        $parser = Parser::query()->where('parsers.uid', $parser_uid)->firstOrFail();
        Parser::remove($parser->q());
    }
}
