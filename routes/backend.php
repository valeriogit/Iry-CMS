<?php

namespace App\Http\Controllers;

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

Route::prefix('admin')->group(function () {
    Route::get('/', [BackendController::class, 'index']);
    Route::get('/update', [BackendController::class, 'checkUpdate']);
    Route::post('/update', [BackendController::class, 'takeUpdate']);

    Route::get('/plugins', [PluginController::class, 'show']);
    Route::get('/plugins/create', [PluginController::class, 'create']);
    Route::post('/plugins/create', [PluginController::class, 'save']);
    Route::get('/plugins/upload', [PluginController::class, 'upload']);
    Route::post('/plugins/upload', [PluginController::class, 'uploaded']);
});
