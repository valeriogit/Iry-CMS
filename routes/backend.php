<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Auth;

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

Route::group(['prefix' => 'admin',  'middleware' => 'isAdmin'], function()
{
    Route::get('/', [BackendController::class, 'index']);
    Route::get('/update', [BackendController::class, 'checkUpdate']);
    Route::post('/update', [BackendController::class, 'takeUpdate']);

    Route::get('/plugins', [PluginController::class, 'show']);
    Route::get('/plugins/create', [PluginController::class, 'create']);
    Route::post('/plugins/create', [PluginController::class, 'save']);
    Route::get('/plugins/upload', [PluginController::class, 'upload']);
    Route::post('/plugins/upload', [PluginController::class, 'uploaded']);
    Route::get('/plugins/delete/{id}', [PluginController::class, 'delete']);
    Route::get('/plugins/download/{id}', [PluginController::class, 'downloadZip']);
    Route::get('/plugins/modify/{id}', [PluginController::class, 'modify']);
    Route::post('/plugins/modify/{id}', [PluginController::class, 'saveModify']);
});
