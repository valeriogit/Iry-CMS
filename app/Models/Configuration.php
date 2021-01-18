<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\SettingsController;

class Configuration extends Model
{
    use HasFactory;

    public $timestamps = false;
}
