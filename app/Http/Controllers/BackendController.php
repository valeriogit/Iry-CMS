<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use ZipArchive;

use App\Models\Configuration;
class BackendController extends Controller
{
  private $config;

  public function __construct(){
    $this->config = Configuration::first();
    $this->activePage = basename($_SERVER['PHP_SELF'], ".php");
  }

  public function checkUpdate(){
    $flag = false;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/valeriogit/Iry-CMS/main/updater/version.json');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);

    if($data != ""){
      $version = json_Decode($data,true);

      for ($i=0; $i < count($version); $i++) {
        if($this->config->iryRawVersion < $version[$i]["id"]){
          $flag = true;
        }
      }
    }

    return $flag;
  }

  public function takeUpdate(){
    if(Auth::user() && Auth::user()->isSuperAdmin()){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/valeriogit/Iry-CMS/main/updater/version.json');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($ch);
      curl_close($ch);

      if($data != ""){
        $version = json_Decode($data,true);

        for ($i=0; $i < count($version); $i++) {
          if($this->config->iryRawVersion < $version[$i]["id"]){
            $url = 'https://raw.githubusercontent.com/valeriogit/Iry-CMS/main/updater/v101.zip';
            // Initialize the cURL session
            $ch = curl_init($url);
            // Inintialize directory name where
            // file will be save
            // Use basename() function to return
            // the base name of file
            $file_name = basename($url);
            // Save file into file location
            $save_file_loc = base_path($file_name);
            // Open file
            $fp = fopen($save_file_loc, 'wb');
            // It set an option for a cURL transfer
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            // Perform a cURL session
            curl_exec($ch);
            // Closes a cURL session and frees all resources
            curl_close($ch);
            // Close file
            fclose($fp);

            $zip = new ZipArchive;
            if($zip->open($save_file_loc) === TRUE) {
              $zip->extractTo(base_path());
              $zip->close();
              unlink($save_file_loc);

              $this->config->iryRawVersion = $version[$i]["id"];
              $this->config->iryVersion = $version[$i]["version"];
              $this->config->save();

              return "Iry CMS updated";
            }
          }
        }
      }
    }
    return "Nessun aggiornamento";
  }

  public function index(){
    if(Auth::user() && Auth::user()->isSuperAdmin())
    {
      return view('backend.content')
        ->with('config', $this->config)
        ->with('activePage', $this->activePage);
    }

    return redirect('login');
  }
}
