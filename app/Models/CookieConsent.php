<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Configuration;

class CookieConsent extends Model
{
    use HasFactory;

    public static function printCookie(){

        $config = Configuration::first();

        if($config->cookieBanner == 1){
            return'<!-- Cookie Consent by https://www.TermsFeed.com -->
            <script type="text/javascript" src="//www.termsfeed.com/public/cookie-consent/3.1.0/cookie-consent.js"></script>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    cookieconsent.run({"notice_banner_type":"headline","consent_type":"express","palette":"dark","language":"en","website_name":"' . $config->nameSite . '", "change_preferences_selector": "#changePreferences"});
                });
            </script>
            <noscript>Cookie Consent by <a href="https://www.TermsFeed.com/" rel="nofollow noopener">TermsFeed</a></noscript>
            <!-- End Cookie Consent -->';
        }

        return '';
    }

    public static function printCookieChanges(){

        if($config->cookieBanner == 1){
            return '<button id="changePreferences" class="btn-outline">Change Preferences</button>';
        }

        return '';
    }
}
