<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AccessoController extends Controller
{
  public function getLogin(){
    //dd(Session::get('errlogin'));
    return view('frontend.login')->with('errlogin', Session::get('errlogin'));
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

    $username = $request->input('username');
    $password = $request->input('password');
    $remember = $request->input('remember');


    if (Auth::attempt(['username' => $username, 'password' => $password], $remember)) {
      dd('autenticato');
    }

    $errlogin = "Username or password incorrect";
    return redirect('login')->with('errlogin',$errlogin);
  }

  public function getRegistration(){
    return view('frontend.registration');
  }

  public function postRegistration(Request $request) {

    $validator = Validator::make($request->all(), [
      'username'  => 'required|unique:users|max:255',
      'email'    => 'required|unique:users|max:255',
      'password'  => 'required',
      'password2' => 'required',
      'terms'     => 'required',
    ]);

    if ($validator->fails()) {
      return redirect('register')->withErrors($validator);
    }

    $user = new User;
    $user->username = $request->input('username');
    $user->email = $request->input('email');
    $user->password = md5($request->input('password').'iry-cms');
    $user->terms = $request->input('terms');
    $user->save();

    return redirect('login');
  }
}
