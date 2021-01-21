<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Configuration;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->config = Configuration::first();
        $this->activePage = "profile";
    }

    public function getProfile()
    {
        return view('backend.profile')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage);
    }
}
