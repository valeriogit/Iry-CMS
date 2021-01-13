<?php

namespace App\Http\Plugins\Database\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\PluginController;

class ControllerDB extends PluginController
{
    private function views($page)
    {
        return PluginController::pluginPage('Database\views\\' . $page);
    }

    public function indexx()
    {
        $provider = file(app_path('Http\Plugins\PluginProvider.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $flag = false;
        $positionInsert = "";

        foreach ($provider as $key => $value) {
            if ($value == "        //Automatic insert of Provider") {
                $positionInsert = $key + 1;
            }
            if ($provider[$key] == '        $calls->Database();') {
                $flag = true;
            }
        }

        if ($flag == false) {
            array_splice($provider, $positionInsert, 0, '        $calls->Database();');
        }

        $myfile = fopen(app_path('Http\Plugins\PluginProvider.php'), "w");
        $provider = implode("\n", $provider);
        $txt = $provider;
        fwrite($myfile, $txt);
        fclose($myfile);

        return $this->views('index');
    }
}
