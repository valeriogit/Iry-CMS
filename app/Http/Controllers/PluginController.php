<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

use App\Models\Configuration;

class PluginController extends Controller
{
  public function pluginPage($page)
  {
    $config = Configuration::first();
    return View::make($page)->with('config',$config);
  }
}
