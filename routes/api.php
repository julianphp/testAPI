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

    Route::fallback(function(){
        return response()->json([
            'error' => 'Url incorrecta.'], 404);
    });
    Route::post('login',[\App\Http\Controllers\api\AuthLoginController::class,'login']);

    Route::prefix('patient')->group(function (){

        Route::group(['middleware' => 'auth:api'], function(){
            Route::post('new', [PatientsController::class,'new']); // ["fullname","dni"]
            Route::post('edit',[PatientsController::class,'edit']);
            Route::post('details',[PatientsController::class,'detail']);
            Route::post('delete',[PatientsController::class,'delete']);
            Route::get('listAll',[PatientsController::class,'listAll']);
        });

    });
