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
* Route for push web notification
*/
Route::post('/push','PushController@store');
Route::get('/push','PushController@push')->name('push');

/*
* Route only for authenticated user
*/
Route::group(['prefix' => 'admin',  'middleware' => 'auth'], function(){
    Route::get('/', [BackendController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile', [ProfileController::class, 'saveProfile']);
});


/*
* Routes where user role is not a subscriber
*/
Route::group(['prefix' => 'admin',  'middleware' => 'checkRole'], function(){
    Route::get('/post/create', [PostController::class, 'createPost']);
    Route::post('/post/upload/file', [PostController::class, 'uploadFile']);
    Route::post('/post/file', [PostController::class, 'listFile']);
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
    Route::post('/settings/saveWebPush', [SettingsController::class, 'saveWebPush']);

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

    Route::get('/user', [ProfileController::class, 'showUser']);
    Route::get('/user/create', [ProfileController::class, 'createUser']);
    Route::post('/user/create', [ProfileController::class, 'saveNewUser']);
    Route::get('/user/modify/{id}', [ProfileController::class, 'getUser']);
    Route::post('/user/modify/{id}', [ProfileController::class, 'saveUser']);
    Route::get('/user/delete/{id}', [ProfileController::class, 'deleteUser']);

    Route::get('/menu', [MenuController::class, 'showMenu']);
    Route::post('/menu/visibility', [MenuController::class, 'changeVisibility']);
    Route::post('/menu/checkname', [MenuController::class, 'checkNameMenu']);
    Route::get('/menu/create', [MenuController::class, 'createMenu']);
    Route::post('/menu/create', [MenuController::class, 'saveMenu']);
    Route::get('/menu/modify/{id}', [MenuController::class, 'modifyMenu']);
    Route::post('/menu/modify/{id}', [MenuController::class, 'updateMenu']);
    Route::get('/menu/delete/{id}', [MenuController::class, 'deleteMenu']);
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
