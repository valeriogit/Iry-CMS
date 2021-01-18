<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Configuration;

class ReCaptcha extends Model
{
    use HasFactory;

    public static function printJS(){
        $config = Configuration::first();

        if($config->recaptcha == 1){
            return '<script src="https://www.google.com/recaptcha/api.js?render=' . $config->recaptchaSite . '"></script>
            <script>
                grecaptcha.ready(function() {
                // do request for recaptcha token
                // response is promise with passed token
                    grecaptcha.execute("' . $config->recaptchaSite . '")
                    .then(function(token){
                        let campi = document.getElementsByName("g-recaptcha-response");
                        for (let i = 0; i < campi.length; i++) {
                            if(campi[i].type == "hidden"){
                                campi[i].value = token;
                            }
                        }
                    });
                });
            </script>';
        }
    }

    public static function printField()
    {
        return '<input type="hidden" name="g-recaptcha-response" class="reCaptcha">';
    }

    public static function checkReCaptcha($request)
    {
        $configuration = Configuration::first();

        $captcha = null;

        if($configuration->recaptcha == 1){
            if($request->has('g-recaptcha-response')){
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $configuration->recaptchaSecret . "&response=" . $request->input('g-recaptcha-response') . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                $data = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($data);

                if($response->success) {
                    if($response->score > 0.3){
                        $captcha = true;
                    }
                }
                else{
                    $captcha = false;
                }
            }
            else{
                $captcha = false;
            }
        }

        return $captcha;
    }
}
