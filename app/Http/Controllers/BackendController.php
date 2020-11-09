<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class BackendController extends Controller
{
    public function updater(){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/valeriogit/Iry-CMS/main/updater/version.json?token=ARJ7SKUEYASSAE5PCTIOHIK7VEU2S');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);

      $version = json_Decode($data,true);
      return $version;
    //  if()
    }

    public function index()
    {
      if(Auth::user() && Auth::user()->isSuperAdmin())
      {
        return view('backend.content');
      }
    }
}
