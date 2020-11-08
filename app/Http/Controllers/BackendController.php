<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class BackendController extends Controller
{
    public function Updater();

    public function index()
    {
      if(Auth::user() && Auth::user()->isSuperAdmin())
      {
        return view('backend.content');
      }
    }
}
