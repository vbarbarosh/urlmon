<?php

use App\Http\Controllers\ArtifactsController;
use App\Http\Controllers\ParsersController;
use App\Http\Controllers\PromisesController;
use App\Http\Controllers\TargetsController;
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

Route::get('/api/v1/targets.json', [TargetsController::class, 'list']);
Route::get('/api/v1/targets/{target_uid}', [TargetsController::class, 'fetch']);
Route::post('/api/v1/targets', [TargetsController::class, 'create']);
Route::post('/api/v1/targets/{target_uid}/parse', [TargetsController::class, 'parse']);
Route::patch('/api/v1/targets/{target_uid}', [TargetsController::class, 'patch']);
Route::delete('/api/v1/targets/{target_uid}', [TargetsController::class, 'remove']);

Route::get('/api/v1/promises.json', [PromisesController::class, 'list']);
Route::get('/api/v1/promises/{promise_uid}', [PromisesController::class, 'fetch']);
Route::delete('/api/v1/promises/{promise_uid}', [PromisesController::class, 'remove']);

Route::get('/api/v1/artifacts.json', [ArtifactsController::class, 'list']);
Route::get('/api/v1/artifacts/{artifact_uid}', [ArtifactsController::class, 'fetch']);
Route::delete('/api/v1/artifacts/{artifact_uid}', [ArtifactsController::class, 'remove']);
