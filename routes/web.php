<?php

use App\Http\Controllers\ParsersController;
use App\Http\Controllers\UrlsController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use function vbarbarosh\laravel_debug_eval;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('/debug/eval', function () {
    user()->should_allow_debug_eval();
    return laravel_debug_eval();
});

Route::get('/login', function () {
    Auth::login(User::query()->firstOrFail());
    return redirect('/');
});

Route::get('/api/v1/parsers.json', [ParsersController::class, 'list']);
Route::get('/api/v1/parsers/{parser_uid}', [ParsersController::class, 'fetch']);
Route::post('/api/v1/parsers', [ParsersController::class, 'create']);
Route::patch('/api/v1/parsers/{parser_uid}', [ParsersController::class, 'patch']);
Route::delete('/api/v1/parsers/{parser_uid}', [ParsersController::class, 'remove']);

Route::get('/api/v1/urls.json', [UrlsController::class, 'list']);
Route::get('/api/v1/urls/{url_uid}', [UrlsController::class, 'fetch']);
Route::post('/api/v1/urls', [UrlsController::class, 'create']);
Route::post('/api/v1/urls/{url_uid}/parse', [UrlsController::class, 'parse']);
Route::patch('/api/v1/urls/{url_uid}', [UrlsController::class, 'patch']);
Route::delete('/api/v1/urls/{url_uid}', [UrlsController::class, 'remove']);
