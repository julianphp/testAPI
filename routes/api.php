<?php

use App\Http\Controllers\api\AuthLoginController;
use App\Http\Controllers\api\PatientsController;
use \App\Http\Controllers\api\DiagnosisController;
use App\Http\Controllers\api\RegisterUserController;
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

    Route::post('register',[RegisterUserController::class,'new']);
    Route::post('login',[AuthLoginController::class,'login']);
    Route::get('logout',[AuthLoginController::class,'logout'])->middleware('auth:api');

    Route::prefix('patient')->group(function (){

        Route::group(['middleware' => 'auth:api'], function(){
            Route::post('new', [PatientsController::class,'new']); // ["fullname","dni"]
            Route::post('edit',[PatientsController::class,'edit']);
            Route::post('details',[PatientsController::class,'detail']);
            Route::post('delete',[PatientsController::class,'delete']);
            Route::get('listAll',[PatientsController::class,'listAll']);
        });

    });

    Route::prefix('diagnosis')->group( function (){
       Route::group(['middleware' => 'auth:api'], function (){
          Route::post('new',[DiagnosisController::class,'new']);
          Route::post('patientListAll',[DiagnosisController::class,'patientListAll']);
       });
    });
