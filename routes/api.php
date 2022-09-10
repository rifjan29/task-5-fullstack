<?php

use App\Http\Controllers\api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('v1')->group(function () {
    Route::post('login',[AuthController::class,'login']);
    Route::post('register',[AuthController::class,'register']);
        Route::middleware(['auth:api'])->group(function () {
            // category
            Route::controller(CategoryController::class)->group(function()
            {
                Route::get('category','index');
                Route::post('category','store');
                Route::get('category/{id}','show');
                Route::patch('category/{id}','update');
                Route::delete('category/{id}','destroy');
            });
            // article
            Route::controller(ArticleController::class)->group(function()
            {
                Route::get('article','index');
                Route::post('article','store');
                Route::get('article/{id}','show');
                Route::put('article/{id}','update');
                Route::delete('article/{id}','destroy');
            });
        });
    });
