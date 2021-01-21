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

/*
* Route only for authenticated user
*/
Route::group(['prefix' => 'admin',  'middleware' => 'auth'], function(){
    Route::get('/', [BackendController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
});


/*
* Routes where user is not a subscriber
*/
Route::group(['prefix' => 'admin',  'middleware' => 'checkRole'], function(){

});


/*
* Routes where user is admin
*/
Route::group(['prefix' => 'admin',  'middleware' => 'isAdmin'], function()
{
    Route::get('/update', [BackendController::class, 'checkUpdate']);
    Route::post('/update', [BackendController::class, 'takeUpdate']);

    /* Settings route */
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings/saveInfoSettings', [SettingsController::class, 'saveInfoSettings']);
    Route::post('/settings/saveRecaptchaSettings', [SettingsController::class, 'saveRecaptchaSettings']);
    Route::post('/settings/saveAnalyticsSettings', [SettingsController::class, 'saveAnalyticsSettings']);

    /* Plugin Route */
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

/*
* Route for load css & js from plugin directory
*/
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

/*
* Route for 404
*/
Route::fallback(function () {

    return view("404");

});
