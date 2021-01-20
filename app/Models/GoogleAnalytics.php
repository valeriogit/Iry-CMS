<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Configuration;

class GoogleAnalytics extends Model
{
    use HasFactory;

    public static function printCode(){
        $configuration = Configuration::first();

        $analyticsCode = '';
        if($configuration->analytics == 1)
        {
            if($configuration->cookieBanner == 1)
            {
                $analyticsCode = str_replace("<script",'<script type="text/plain" cookie-consent="tracking"', $configuration->analyticsCode);
            }
            else
            {
                $analyticsCode = $configuration->analyticsCode;
            }
        }

        return $analyticsCode;
    }
}
