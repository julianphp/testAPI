<?php

use App\Models\Patients;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    Log::channel('writeinlog','sdfsdfsdfsdf');
    $aa = Patients::dni('12121212Z')->first();
    if ($aa){
       // dd('dfsdf');
    }
    dd($aa);
    return view('welcome');
});
