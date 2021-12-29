<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::resource('/categories', CategoryController::class);
Route::resource('/products', ProductController::class);
Route::get('/restore/{id}', [ProductController::class, 'restore']);
Route::get('/restoreAll', [ProductController::class, 'restoreAll']);
Route::get('/search', [ProductController::class, 'search']);
Route::get('/sort', [ProductController::class, 'sort']);


//////comment and like/////////

Route::post('/comment/{id}', [ProductController::class, 'comment']);
Route::get('/comment/{id}', [ProductController::class, 'showComments']);

Route::post('/like/{id}', [ProductController::class, 'liker']);



Route::get('/lang/{locale}', function ($locale, Request $request) {
    if (!in_array($locale, ['ar', 'en'])) {
        abort(400);
    }
    App::setLocale($locale);
    ///$request->session()->put('locale', $locale);
    // return Session::get('locale');
    $ll = __('auth.email');
    return $ll;
});
