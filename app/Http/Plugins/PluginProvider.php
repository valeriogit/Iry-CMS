<?php
namespace App\Http\Plugins;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class PluginProvider extends ServiceProvider {
    public function boot()
    {
        $calls = new PluginProvider('');
        //Automatic insert of Provider
        $calls->Database();
    }
    public function __call($name, $arguments)
    {
      return Route::middleware('web')
      ->group(app_path('\Http\Plugins\\' .$name.'\routes\web.php'));
    }
}