<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Configuration;

class PostController extends Controller
{
    protected $POST = "post";
    protected $MANAGEPOST = "managePost";

    public function __construct()
    {
        $this->config = Configuration::first();
        $this->activePage = $this->POST;
    }

    public function createPost(){
        return view('backend.createPost')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage);
    }

    public function uploadFile(Request $request){
        return asset('img/logoIry.png');
        return "lifgdfgsdfgfgdgsfgnk";
    }
}
