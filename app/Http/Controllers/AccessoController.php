<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Mail\ValidationEmail;
use App\Models\User;
use App\Models\Configuration;

class AccessoController extends Controller
{
  private $config;

  public function __construct() {
    $this->config = Configuration::first();
  }

  public function getLogin() {
    if(Auth::check()){
      return redirect('/');
    }

    return view('frontend.login')
            ->with('errlogin', Session::get('errlogin'))
            ->with('config', $this->config);
  }

  public function postLogin(Request $request) {
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
        return redirect('home');
      }
    }

    $errlogin = "Username or password incorrect";
    return redirect('login')->with('errlogin',$errlogin);
  }

  public function Logout() {
    if(Auth::check())
    {
      Auth::logout();
    }

    return redirect('/');
  }

  public function getRegistration(){
    if(Auth::check()){
      return redirect('/');
    }

    return view('frontend.registration')
      ->with('mailSent', Session::get('mailSent'))
      ->with('config', $this->config);
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

    if($this->config->emailValidation){
      $user->emailValidate = md5($request->email);
      Mail::to('valerio.palazzo@gmail.com')->send(new ValidationEmail($this->config,$user));
    }

    $user->save();

    $mailSent = "Validate the email sent to your address";
    return redirect('register')->with('mailSent',$mailSent);
  }

  public function validateMail($token) {
    if($token != "")
    {
      $user = User::where('emailValidate', '=',$token)->first();
      $user->emailValidate = 1;
      $user->save();
    }

    return redirect('login');
  }

  public function getForgotPassword()
  {
    if(Auth::check()){
      return redirect('/');
    }

    return view('frontend.forgotPassword')
      ->with('mailSent', Session::get('mailSent'))
      ->with('config', $this->config);
  }

  public function postForgotPassword(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required'
    ]);

    if ($validator->fails()) {
      return redirect('getForgotPassword')->withErrors($validator);
    }

    $user = User::where('email', '=', $request->email);

    $mailSent = "Username or password incorrect";

    if($user){
      $user->forgotPassword = md5($request->email);
      $user->save();
      $mailSent = "Check your email for password reset";
    }

    return redirect('getForgotPassword')->with('$mailSent',$mailSent);
  }
}
