<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

    public function getProfile()
    {
        $user = User::find(Auth::user()->id);

        $roles = Role::all();

        return view('backend.profile')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage)
            ->with('user', $user)
            ->with('roles', $roles);
    }

    public function saveProfile(Request $request)
    {
        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorProfile', 'fail');
            return redirect()->action([ProfileController::class, 'getProfile']);
        }

        $user = User::find(Auth::user()->id);

        if(Role::checkRole(['administrator'])){
            $validated = $request->validate([
                'username' => 'required|max:250',
                'password' => 'nullable|max:250',
                'email' => 'required|max:250',
                'role' => 'required',
                'avatar' => 'nullable|image|mimes:png,jpg,jpeg'
            ]);

            $user->username = $request->username;
            $user->email = $request->email;
            $user->username = $request->username;

            if($request->role==1){
                $request->is_super_admin = 1;
            }
            else{
                $request->is_super_admin = 0;
            }
            $user->role = $request->role;
        }
        else
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

    public function createUser()
    {
        $roles = Role::all();

        $this->activePage = "manageUser";
        return view('backend.createUser')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage)
            ->with('roles', $roles);
    }

    public function saveNewUser(Request $request){
        $validated = $request->validate([
            'username' => 'required|max:250',
            'password' => 'required|max:250',
            'email' => 'required|max:250',
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg'
        ]);

        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorProfile', 'fail');
            return redirect()->action([ProfileController::class, 'createUser']);
        }

        $user = new User;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;

        $user->save();

        $path = public_path('img');
        if($request->has('avatar')){
            $request->avatar->move($path.'/avatar/', $user->id.$request->avatar->getClientOriginalName());
            $user->avatar = '/avatar/'.$user->id.$request->avatar->getClientOriginalName();
            Image::make($path.$user->avatar)->resize(512, 512)->save($path.$user->avatar);

            $user->save();
        }

        session()->flash('saveUser', 'saved');
        return redirect()->action([ProfileController::class, 'showUser']);
    }

    public function showUser(){
        $users = User::all();
        $this->activePage = "manageUser";

        return view('backend.showUsers')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage)
            ->with('users', $users);
    }

    public function deleteUser($id){
        try {
            User::destroy($id);
            session()->flash('deletedSuccessUser', 'deleted');
            return redirect()->action([ProfileController::class, 'showUser']);

        } catch (\Exception $e) {
            session()->flash('deletedFailUser', 'deleted');
            return redirect()->action([ProfileController::class, 'showUser']);
        }
    }

    public function getUser($id)
    {
        $user = User::find($id);
        $roles = Role::all();
        $this->activePage = "manageUser";

        return view('backend.modifyUser')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage)
            ->with('user', $user)
            ->with('roles', $roles);
    }

    public function saveUser(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'required|max:250',
            'password' => 'nullable|max:250',
            'email' => 'required|max:250',
            'role' => 'required',
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg'
        ]);

        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorProfile', 'fail');
            return redirect()->action([ProfileController::class, 'showUser']);
        }

        $user = User::find($id);

        $user->username = $request->username;
        $user->email = $request->email;
        $user->username = $request->username;

        if($request->role==1){
            $user->is_super_admin = 1;
        }
        else{
            $user->is_super_admin = 0;
        }

        $user->role = $request->role;

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
        session()->flash('updateSuccessUser', 'saved');
        return redirect()->action([ProfileController::class, 'showUser']);
    }
}
