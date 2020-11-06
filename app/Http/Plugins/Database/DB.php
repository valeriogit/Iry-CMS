<?php

namespace App\Http\Plugins\Database;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;

class DB extends BackendController
{
  public function indexx()
  {
    $emails = file(app_path('Providers\RouteServiceProvider.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($emails as $key => $value) {
      if($value=="        //Automatic insert of Provider")
      {
        if($emails[$key+1]!= "        require_once app_path('\Http\Plugins\Users\Providers\RouteServiceProvider.php');")
        {
          array_splice($emails, $key+1, 0, "        require_once app_path('\Http\Plugins\Users\Providers\RouteServiceProvider.php');");
        }
      }
    }

    $myfile = fopen(app_path('Providers\RouteServiceProvider.php'), "w") or die("Unable to open file!");
    $emails = implode("\n",$emails);
    $txt = $emails;
    fwrite($myfile, $txt);
    fclose($myfile);
    return Parent::pluginPage('Database\view\index');
  }
}
