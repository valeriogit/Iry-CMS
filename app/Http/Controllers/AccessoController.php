<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Mail\ValidationEmail;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Models\Configuration;
use App\Models\ReCaptcha;

class AccessoController extends Controller{
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

    $captcha = ReCaptcha::checkReCaptcha($request);

    if($captcha === false){
        $errlogin = "Incorrect reCaptcha";
        session()->flash('errlogin', $errlogin);
        return redirect()->action([AccessoController::class, 'getLogin']);
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

    $captcha = ReCaptcha::checkReCaptcha($request);
    if($captcha === false){
        $errRegistration = "Incorrect reCaptcha";
        session()->flash('mailSent', $errRegistration);
        return redirect()->action([AccessoController::class, 'getRegistration']);
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

  public function getForgotPassword() {
    if(Auth::check()){
      return redirect('/');
    }

    return view('frontend.forgotPassword')
      ->with('mailSent', Session::get('mailSent'))
      ->with('config', $this->config);
  }

  public function postForgotPassword(Request $request) {
    $validator = Validator::make($request->all(), [
      'email' => 'required'
    ]);

    if ($validator->fails()) {
      return redirect('forgotPassword')->withErrors($validator);
    }

    $captcha = ReCaptcha::checkReCaptcha($request);
    if($captcha === false){
        $errRegistration = "Incorrect reCaptcha";
        return redirect()->with('mailSent', $errRegistration)->action([AccessoController::class, 'getForgotPassword']);
    }

    $user = User::where('email', '=', $request->email)->first();

    $mailSent = "Email incorrect";

    if($user){

      $user->forgotPassword = md5($request->email);
      $user->save();

      $mailSent = "Check your email for password reset";
      Mail::to('valerio.palazzo@gmail.com')->send(new ResetPassword($this->config,$user));
    }

    return redirect('forgotPassword')->with('mailSent',$mailSent);
  }

  public function getResetPassword($token) {
    if($token != "")
    {
      return view('frontend.resetPassword')
        ->with('tokenPass', $token)
        ->with('config', $this->config);
    }

    return redirect('login');
  }

  public function postResetPassword(Request $request) {
    $validator = Validator::make($request->all(), [
      'password'  => 'required',
      'password2' => 'required'
    ]);

    if ($validator->fails()) {
      return redirect('resetPassword')->withErrors($validator);
    }

    $captcha = ReCaptcha::checkReCaptcha($request);
    if($captcha === false){
        $errRegistration = "Incorrect reCaptcha";
        session()->flash('mailSent', $errRegistration);
        return redirect()->action([AccessoController::class, 'getLogin']);
    }

    $user = User::where('forgotPassword', '=', $request->tokenPass)->first();

    if($user){
      $user->forgotPassword = "";
      $user->password = Hash::make($request->password);
      $user->save();
    }

    return redirect('login');
  }
}
