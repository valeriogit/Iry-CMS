<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\Configuration;
use App\Models\ReCaptcha;
use App\Models\EventPushNotification;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->config = Configuration::first();
        $this->activePage = "settings";
    }

    public function index()
    {
        $eventPush = EventPushNotification::first();
        return view('backend.settings')
                ->with('config', $this->config)
                ->with('activePage', $this->activePage)
                ->with('eventPush', $eventPush);
    }

    public function saveInfoSettings(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg',
            'favicon' => 'nullable',
            'validationEmail' => 'nullable',
            'cookieBanner' => 'nullable'
        ]);

        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }

        try{
            $configuration = Configuration::first();
            $configuration->nameSite = $request->name;

            $path = public_path('');

            if($request->has('favicon')){
                $request->favicon->move($path, 'favicon.ico');
            }

            $path = public_path('img');

            if($request->has('logo')){
                $request->logo->move($path, $request->logo->getClientOriginalName());
                $configuration->logoSite = "/".$request->logo->getClientOriginalName();
            }

            if($request->has('icon')){
                $request->icon->move($path, $request->icon->getClientOriginalName());
                $configuration->icoSite = "/".$request->icon->getClientOriginalName();
            }

            if($request->validationEmail == null)
            {
                $configuration->emailValidation = 0;
            }
            else{
                $configuration->emailValidation = 1;
            }

            if($request->cookieBanner == null)
            {
                $configuration->cookieBanner = 0;
            }
            else{
                $configuration->cookieBanner = 1;
            }

            $configuration->save();

            session()->flash('savedSettings', 'installed');
            return redirect()->action([SettingsController::class, 'index']);
        }catch(\Exception $e){
            //dd($e);
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }
    }

    public function saveRecaptchaSettings(Request $request)
    {
        $validated = $request->validate([
            'reCaptchaValue' => 'nullable',
            'reCaptchaPublic' => 'nullable|max:250',
            'reCaptchaPrivate' => 'nullable|max:250',
        ]);

        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }

        try {
            if($request->reCaptchaValue != null){
                if($request->reCaptchaPublic != "" && $request->reCaptchaPrivate != ""){
                    $configuration = Configuration::first();

                    $configuration->recaptcha = 1;
                    $configuration->recaptchaSite = $request->reCaptchaPublic;
                    $configuration->recaptchaSecret = $request->reCaptchaPrivate;

                    $configuration->save();

                    session()->flash('savedSettings', 'installed');
                    return redirect()->action([SettingsController::class, 'index']);
                }
                else{
                    session()->flash('errorSettings', 'fail');
                    return redirect()->action([SettingsController::class, 'index']);
                }
            }else{
                $configuration = Configuration::first();

                $configuration->recaptcha = 0;

                $configuration->save();
            }

            session()->flash('savedSettings', 'saved');
            return redirect()->action([SettingsController::class, 'index']);
        } catch (\Exception $e) {
            //dd($e);
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }
    }

    public function saveAnalyticsSettings(Request $request)
    {
        $validated = $request->validate([
            'analytics' => 'nullable',
            'analyticsCode' => 'nullable'
        ]);

        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }

        try {
            if($request->analytics != null){
                if($request->analyticsCode != ""){

                    $configuration = Configuration::first();

                    $configuration->analytics = 1;
                    $configuration->analyticsCode = $request->analyticsCode;

                    $configuration->save();

                    session()->flash('savedSettings', 'installed');
                    return redirect()->action([SettingsController::class, 'index']);
                }
                else{
                    session()->flash('errorSettings', 'installed');
                    return redirect()->action([SettingsController::class, 'index']);
                }
            }else{
                $configuration = Configuration::first();

                $configuration->analytics = 0;

                $configuration->save();
            }

            session()->flash('savedSettings', 'saved');
            return redirect()->action([SettingsController::class, 'index']);
        } catch (\Exception $e) {
            //dd($e);
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }
    }

    public function saveWebPush(Request $request)
    {
        $validated = $request->validate([
            'webPush' => 'nullable|max:250',
            'vapidSite' => 'nullable|max:250',
            'vapidSecret' => 'nullable|max:250',
            'iconNotification' => 'nullable|image|mimes:png,jpg,jpeg',
            'iconBadge' => 'nullable|image|mimes:png,jpg,jpeg'
        ]);

        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }

        try{
            if($request->webPush != null){
                if($request->vapidSite != "" && $request->vapidSecret != ""){
                    $configuration = Configuration::first();

                    $configuration->webPush = 1;
                    $configuration->vapidSite = $request->vapidSite;
                    $configuration->vapidSecret = $request->vapidSecret;

                    $configuration->save();

                    $path = public_path('img/webpush');

                    if($request->has('iconNotification')){
                        $request->iconNotification->move($path, $request->iconNotification->getClientOriginalName());
                        $configuration->iconNotification = "/img/webpush/".$request->iconNotification->getClientOriginalName();
                    }

                    if($request->has('iconBadge')){
                        $request->iconBadge->move($path, $request->iconBadge->getClientOriginalName());
                        $configuration->iconBadge = "/img/webpush/".$request->iconBadge->getClientOriginalName();
                    }

                    $eventPush = EventPushNotification::first();

                    if($request->webPost != null){
                        $eventPush->post = 1;
                    }
                    else{
                        $eventPush->post = 0;
                    }

                    if($request->webUser != null){
                        $eventPush->user = 1;
                    }
                    else{
                        $eventPush->user = 0;
                    }

                    $eventPush->save();
                }
                else{
                    session()->flash('errorSettings', 'fail');
                    return redirect()->action([SettingsController::class, 'index']);
                }
            }else{
                $configuration = Configuration::first();

                $configuration->webPush = 0;

                $configuration->save();
            }

            $configuration->save();

            session()->flash('savedSettings', 'installed');
            return redirect()->action([SettingsController::class, 'index']);
        }catch(\Exception $e){
            dd($e);
            session()->flash('errorSettings', 'fail');
            return redirect()->action([SettingsController::class, 'index']);
        }
    }
}
