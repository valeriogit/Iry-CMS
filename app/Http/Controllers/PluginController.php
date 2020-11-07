<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class PluginController extends Controller
{
  public function pluginPage($page)
  {
    return View::make($page);
  }
}
