<?php
namespace App\Http\Plugins;

use Illuminate\Support\Facades\Route;
use App\Http\Plugins\Database\Controller\ControllerDB;

Route::get('/db', [ControllerDB::class, 'indexx']);
