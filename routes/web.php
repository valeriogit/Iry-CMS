<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

use ZipArchive;
use Valeriogit\Tournament\Tournament;
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

Route::get('/login', [AccessoController::class, 'getLogin'])->name('login');
Route::post('/login', [AccessoController::class, 'postLogin']);

Route::get('/logout', [AccessoController::class, 'Logout']);

Route::get('/register', [AccessoController::class, 'getRegistration']);
Route::post('/register', [AccessoController::class, 'postRegistration']);

Route::get('/validateMail/{token}', [AccessoController::class, 'validateMail']);

Route::get('/forgotPassword', [AccessoController::class, 'getForgotPassword']);
Route::post('/forgotPassword', [AccessoController::class, 'postForgotPassword']);

Route::get('/resetPassword/{token}', [AccessoController::class, 'getResetPassword']);
Route::post('/resetPassword', [AccessoController::class, 'postResetPassword']);

Route::get('/assets/{author}/{plugin}/{folder}/{file}', [ function ($author, $plugin, $folder, $file) {

    $path = app_path("Http/Plugins/$author/$plugin/resources/$folder/$file");

    if (\File::exists($path)) {
        if($folder == 'js'){
            return response()->file($path, array('Content-Type' => 'application/javascript'));
        }else{
            return response()->file($path, array('Content-Type' => 'text/css'));
        }
    }

    return response()->json([ ], 404);
}]);
