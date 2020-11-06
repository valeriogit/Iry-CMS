<?php  Route::middleware('web')
    ->namespace($this->namespace)
    ->group(function ($router) {
      require app_path('\Http\Plugins\Database\routes\web.php');
    });
