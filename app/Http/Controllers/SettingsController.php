<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\Configuration;
use App\Models\Menu;
use App\Models\MenuList;
use App\Models\MenuVoice;
use App\Models\ReCaptcha;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->config = Configuration::first();
        $this->activePage = "settings";
        $this->menu = DB::table('menu')->join('menuvoice', 'menu.idMenuVoice', '=', 'menuvoice.id')->select('menuvoice.name', 'menuvoice.url', 'menuvoice.slug', 'menuvoice.icon')->get();
    }

    public function index(){

        return view('backend.settings')
                ->with('config', $this->config)
                ->with('activePage', $this->activePage)
                ->with('menu', $this->menu);
    }

    public function saveInfoSettings(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg',
            'favicon' => 'nullable',
            'validationEmail' => 'nullable'
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

            $path = public_path('images');

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

            $configuration->save();

            session()->flash('savedSettings', 'installed');
            return redirect()->action([SettingsController::class, 'index']);
        }catch(\Exception $e){
            //dd($e);
            session()->flash('errorSettings', 'installed');
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
                    session()->flash('errorSettings', 'installed');
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
}
