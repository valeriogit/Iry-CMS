<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class BackendController extends Controller
{
    public function updater(){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/valeriogit/Iry-CMS/main/updater/.env?token=ARJ7SKXZUHVF4GPPLN5WOZK7VERFS');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);

    return $data;
    }

    public function index()
    {
      if(Auth::user() && Auth::user()->isSuperAdmin())
      {
        return view('backend.content');
      }
    }
}
