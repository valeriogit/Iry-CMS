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
Route::get('/login', [AccessoController::class, 'getLogin']);
Route::post('/login', [AccessoController::class, 'postLogin']);
Route::get('/logout', [AccessoController::class, 'Logout']);
Route::get('/register', [AccessoController::class, 'getRegistration']);
Route::post('/register', [AccessoController::class, 'postRegistration']);

Route::get('/admin', [BackendController::class, 'index']);
