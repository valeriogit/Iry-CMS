<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Log;
use Auth;

class BackendController extends Controller
{  
    public function pluginPage($page)
    {
      return View::make($page);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if(Auth::user())
      {
        return view('backend.content');
      }
    }
}
