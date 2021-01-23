<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Configuration;
use App\Models\User;
use App\Models\ReCaptcha;
use App\Models\Role;
use Intervention\Image\ImageManagerStatic as Image;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->config = Configuration::first();
        $this->activePage = "profile";
    }

    public function getProfile($id = null)
    {
        if($id != null)
        {
            $user = User::find($id);
        }
        else
        {
            $user = User::find(Auth::user()->id);
        }

        return view('backend.profile')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage)
            ->with('user', $user);
    }

    public function saveProfile(Request $request, $id = null)
    {
        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorProfile', 'fail');
            return redirect()->action([ProfileController::class, 'getProfile']);
        }

        if($id != null)
        {
            $user = User::find($id);
        }
        else
        {
            $user = User::find(Auth::user()->id);
        }

        if(Role::checkRole(['administrator'])){
            $validated = $request->validate([
                'username' => 'required|max:250',
                'password' => 'nullable|max:250',
                'email' => 'required|max:250',
                'avatar' => 'nullable|image|mimes:png,jpg,jpeg'
                ]);

            $user->username = $request->username;
            $user->email = $request->email;
            $user->username = $request->username;
        }else
        {
            $validated = $request->validate([
                'password' => 'nullable|max:250',
                'avatar' => 'nullable|image|mimes:png,jpg,jpeg'
            ]);
        }

        if($request->password != ""){
            $user->password = Hash::make($request->password);
        }

        $path = public_path('img');
        if($request->has('avatar')){
            $request->avatar->move($path.'/avatar/', $user->id.$request->avatar->getClientOriginalName());
            $user->avatar = '/avatar/'.$user->id.$request->avatar->getClientOriginalName();
            Image::make($path.$user->avatar)->resize(512, 512)->save($path.$user->avatar);
        }

        $user->save();
        session()->flash('saveProfile', 'saved');
        return redirect()->action([ProfileController::class, 'getProfile']);
    }
}
