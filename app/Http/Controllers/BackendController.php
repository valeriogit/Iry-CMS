<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class BackendController extends Controller
{
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
