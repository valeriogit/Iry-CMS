<?php
namespace App\Http\Plugins;

use Illuminate\Support\Facades\Route;
use App\Http\Plugins\valerio\prova\Controller\Controllerprova;

//If you want a frontend link remove /admin

Route::get('/admin/valerio/prova/', [Controllerprova::class, 'index']);