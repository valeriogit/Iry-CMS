<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Configuration;

class AccessoController extends Controller
{
  private $config;

  public function __construct()
  {
    $this->config = Configuration::first();
  }

  public function getLogin(){
    return view('frontend.login')
            ->with('errlogin', Session::get('errlogin'))
            ->with('config', $this->config);
  }

  public function postLogin(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'username'  => 'required',
      'password'  => 'required'
    ]);

    if ($validator->fails()) {
      return redirect('login')->withErrors($validator);
    }

    $username = $request->username;
    $password = $request->password;
    $remember = $request->remember;

    if (Auth::attempt(['username' => $username, 'password' => $password], $remember)) {
      if (Auth::user()->isSuperAdmin()) {
        return redirect('admin');
      }
      else {
        //return redirect('home');
      }
    }

    $errlogin = "Username or password incorrect";
    return redirect('login')->with('errlogin',$errlogin);
  }

  public function getRegistration(){
    return view('frontend.registration')->with('config', $this->config);
  }

  public function postRegistration(Request $request) {

    $validator = Validator::make($request->all(), [
      'username'  => 'required|unique:users|max:255',
      'email'     => 'required|unique:users|max:255',
      'password'  => 'required',
      'password2' => 'required',
      'terms'     => 'required',
    ]);

    if ($validator->fails()) {
      return redirect('register')->withErrors($validator);
    }

    $user = new User;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->terms = $request->terms;
    $user->save();

    return redirect('login');
  }
}
