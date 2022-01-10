<?php

use App\Http\Controllers\api\PatientsController;
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
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
    Route::post('login',[\App\Http\Controllers\api\AuthLoginController::class,'login']);

    Route::prefix('patient')->group( function (){
        Route::get('new/{name}/{dni}', [PatientsController::class,'new']);

        //Route::group(['middleware' => 'auth:api'], function(){
            Route::post('data/{dni}',[PatientsController::class,'details'])->middleware('auth:api');
        //});

    });
