<?php

namespace App\Http\Plugins\valerio\prova\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\PluginController;

class Controllerprova extends PluginController
{
    private function views($page)
    {
        return PluginController::pluginPage("valerio\prova\\views\\" . $page, "valerio_prova");
    }

    public function index()
    {
    	return $this->views("index");
    }
}