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

Route::get('/login', [AccessoController::class, 'getLogin']);
Route::post('/login', [AccessoController::class, 'postLogin']);

Route::get('/logout', [AccessoController::class, 'Logout']);

Route::get('/register', [AccessoController::class, 'getRegistration']);
Route::post('/register', [AccessoController::class, 'postRegistration']);

Route::get('/validateMail/{token}', [AccessoController::class, 'validateMail']);

Route::get('/forgotPassword', [AccessoController::class, 'getForgotPassword']);
Route::post('/forgotPassword', [AccessoController::class, 'postForgotPassword']);

Route::get('/resetPassword/{token}', [AccessoController::class, 'getResetPassword']);
Route::post('/resetPassword', [AccessoController::class, 'postResetPassword']);


Route::get('example', function () {

    file_put_contents(
        "master.zip",
        file_get_contents("https://github.com/valeriogit/tournament/archive/main.zip")
    );

    $download_file_loc = public_path('master.zip');
    $save_file_loc = base_path('vendor/valeriogit');

    $zip = new ZipArchive;
    if ($zip->open($download_file_loc) === TRUE) {

        //we extract the zip
        $zip->extractTo($save_file_loc);
        $zip->close();
        unlink($download_file_loc);

        //we take the directory created from unzipped
        $checkdir = scandir($save_file_loc, 1);
        $dir = array_diff($checkdir, array('.', '..'));
        $dir = $dir[0];

        //we rename it
        rename($save_file_loc . "/" . $dir, $save_file_loc . "/tournament");

        dd($dir);

        return "Iry CMS updated";
    }
});
