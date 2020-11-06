<?php
namespace App\Http\Plugins;

use Illuminate\Support\Facades\Route;
use App\Http\Plugins\Database\DB;

Route::get('/db', [DB::class, 'indexx']);
