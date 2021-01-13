<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use ZipArchive;

use App\Models\Configuration;
use App\Models\Plugin;
use Illuminate\Support\Facades\File;

class PluginController extends Controller
{
    public function pluginPage($page)
    {
        //we call this funciont from plugin for make views
        //and we call in this controller too
        $config = Configuration::first();
        $activePage = basename($_SERVER['PHP_SELF'], ".php");
        return View::make($page)
            ->with('config', $config)
            ->with('activePage', $activePage);
    }

    public function show()
    {
        $plugins = Plugin::all();

        return $this->pluginPage('backend.showPlugins')
            ->with('plugins', $plugins);
    }

    public function create()
    {
        return $this->pluginPage('backend.createPlugin');
    }

    public function upload()
    {
        return $this->pluginPage('backend.uploadPlugin');
    }

    public function uploaded(Request $request)
    {
        $validated = $request->validate([
            'zip' => 'required|file|mimes:zip'
        ]);

        try {
            //check the upload
            if ($request->hasFile('zip')) {
                if ($request->file('zip')->isValid()) {
                    //save file uploaded
                    $nameFileComplete = $request->zip->getClientOriginalName();

                    //take name file and check dir
                    $file_name = pathinfo($nameFileComplete, PATHINFO_FILENAME);

                    //esplodo nome e prendo prima parte che è autore
                    $file_name = explode("_", $file_name);

                    if (is_array($file_name)) {
                        $author = $file_name[0];

                        //ricostruisco nome
                        array_shift($file_name);

                        $file_name = implode("_", $file_name);
                    }

                    $pathPlugins = app_path('Http/Plugins/' . $author);

                    if (!file_exists($pathPlugins)) {
                        File::makeDirectory($pathPlugins);
                    }

                    if (!file_exists($pathPlugins . "/" . $file_name)) {
                        $path = $request->zip->storeAs('tmpUpload', $nameFileComplete);
                        $path = storage_path('app/' . $path);
                        //extract the plugin
                        $zip = new ZipArchive;
                        if ($zip->open($path) === TRUE) {
                            $zip->extractTo($pathPlugins);
                            $zip->close();
                            unlink($path);
                        }

                        //install plugin
                        $this->installPlugin($author, $file_name);

                        //save on db
                        $plugin = new Plugin;

                        $pathPlugins = $pathPlugins . "/" . $file_name;
                        if (file_exists($pathPlugins . "/info.json")) {
                            $strJsonFileContents = file_get_contents($pathPlugins . "/info.json");
                            // Convert to array
                            $array = json_decode($strJsonFileContents, true);
                            dd($array);
                        }



                        return redirect()->action([PluginController::class, 'show']);
                    }

                    session()->flash('errorPlugin', 'A plugin with name: "' . $file_name . '" of: "' . $author . '" already exist.');
                    return back();
                }
            }

            session()->flash('errorPlugin', 'An error occurred, retry please!');
            return back();
        } catch (\Exception $e) {
            dd($e);
            session()->flash('errorPlugin', 'An error occurred, retry please!');
            return back();
        }
    }

    private function installPlugin($author, $pluginName)
    {
        $provider = file(app_path('Http\Plugins\PluginProvider.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $flag = false;
        $positionInsert = "";

        foreach ($provider as $key => $value) {
            if ($value == "        //Automatic insert of Provider") {
                $positionInsert = $key + 1;
            }
            if ($provider[$key] == '        $calls->' . $pluginName . '();') {
                $flag = true;
            }
        }

        if ($flag == false) {
            array_splice($provider, $positionInsert, 0, '        $calls->' . $pluginName . '();');
        }

        foreach ($provider as $key => $value) {
            if ($value == "                //Automatic insert of Provider") {
                $positionInsert = $key + 1;
            }
            if ($provider[$key] == '                app_path("\Http\Plugins\\\\' . $author . '\\\\" . $name  . "\\\\routes\web.php"),') {
                $flag = true;
            }
        }

        if ($flag == false) {
            array_splice($provider, $positionInsert, 0, '                app_path("\Http\Plugins\\\\' . $author . '\\\\" . $name  . "\\\\routes\web.php"),');
        }

        $myfile = fopen(app_path('Http\Plugins\PluginProvider.php'), "w");
        $provider = implode("\n", $provider);
        $txt = $provider;
        fwrite($myfile, $txt);
        fclose($myfile);
    }



    /*
    * Funzioni per salvare e creare il plugin
    */

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:250',
            'description' => 'required',
            'author' => 'required|max:250',
            'author-email' => 'nullable|email|max:250'
        ]);

        try {
            $name = $request->input('name');
            $description = $request->input('description');
            $author = $request->input('author');
            $email = $request->input('author-email');
            $path = public_path('tmp/' . $author . '/');

            //create folder if doesn't exist
            if (!file_exists($path)) {
                File::makeDirectory($path);
            }

            $path = public_path('tmp/' . $author . '/' . $name . '/');

            //create folder if doesn't exist
            if (!file_exists($path)) {
                File::makeDirectory($path);
            } else {
                //delete all content of folder
                File::deleteDirectory($path);
                /*$files = glob($path . '*'); // get all file names
                foreach ($files as $file) { // iterate files
                    if (is_file($file)) {
                        unlink($file); // delete file
                    }
                }*/
            }

            //funzioni che creano i diversi file
            $this->createModelPlugin($path, $author, $name);
            $this->createControllerPlugin($path, $author, $name);
            $this->createRoutePlugin($path, $author, $name);
            $this->createViewPlugin($path);
            $this->createReadmePlugin($path, $description);
            $this->createInfoPlugin($path, $name, $description, $author, $email);

            //una volta andato a buon fine creaiamo file zip
            $path = public_path('tmp/' . $author . '/');
            $this->zipPlugin($path, $author, $name);
            session()->flash('downloadPlugin', $author . '/' . $author . "_" . $name . ".zip");
            return redirect()->action([PluginController::class, 'create']);
        } catch (\Exception $e) {
            //dd($e);
            //elimina la cartella creata in caso di errore
            $path = public_path('tmp/' . $author . '/');
            File::deleteDirectory($path);
            session()->flash('errorPlugin', 'An error occurred, retry please!');
            //session(['errorPlugin' => 'An error occurred, retry please!']);
            return back()->withInput();
        }
    }

    private function zipPlugin($path, $author, $name)
    {
        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($path . $author . '_' . $name . ".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($path));

            if (!$file->isDir()) {
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            } else {
                if ($relativePath !== false)
                    $zip->addEmptyDir($relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }

    private function createModelPlugin($path, $author, $name)
    {
        $myfile = $path . "\\" . $name . ".php";
        $txt = '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ' . $name . ' extends Model
{
    use HasFactory;

    protected $table = "' . $author . '_' . $name . '";
}';
        File::put($myfile, $txt);
    }

    private function createControllerPlugin($path, $author, $name)
    {
        //creating contoller
        //create folder if doesn't exist
        $path = $path . "Controller/";
        if (!file_exists($path)) {
            File::makeDirectory($path);
        }

        //create file and insert text
        $myfile = $path . "Controller" . $name . ".php";
        $txt = '<?php

namespace App\Http\Plugins\\' . $author . '\\' . $name . '\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\PluginController;

class Controller' . $name . ' extends PluginController
{
    private function views($page)
    {
        return PluginController::pluginPage("' . $author . '\\' . $name . '\\\views\\\" . $page);
    }

    public function index()
    {
    	return $this->views("index");
    }
}';
        File::put($myfile, $txt);
    }

    private function createRoutePlugin($path, $author, $name)
    {
        //creating routes
        //create folder if doesn't exist
        $path = $path . "routes/";
        if (!file_exists($path)) {
            File::makeDirectory($path);
        }

        //create file and insert text
        $myfile = $path . "web.php";
        $txt = "<?php
namespace App\Http\Plugins;

use Illuminate\Support\Facades\Route;
use App\Http\Plugins\\" . $author . '\\' . $name . "\Controller\Controller" . $name . ";

Route::get('/" . $author . '/' . $name . "/', [Controller" . $name . "::class, 'index']);";

        File::put($myfile, $txt);
    }

    private function createViewPlugin($path)
    {
        //creating views
        //create folder if doesn't exist
        $path = $path . "views/";
        if (!file_exists($path)) {
            File::makeDirectory($path);
        }

        //create file and insert text
        $myfile = $path . "index.blade.php";
        $txt = '@extends("backend.app")

@section("content")
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">Starter Page</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Starter Page</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">Card title</h5>

                <p class="card-text">
                    Some quick example text to build on the card title and make up the bulk of the card\'s
                    content.
                </p>

                <a href="#" class="card-link">Card link</a>
                <a href="#" class="card-link">Another link</a>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-body">
                <h5 class="card-title">Card title</h5>

                <p class="card-text">
                    Some quick example text to build on the card title and make up the bulk of the card\'s
                    content.
                </p>
                <a href="#" class="card-link">Card link</a>
                <a href="#" class="card-link">Another link</a>
                </div>
            </div><!-- /.card -->
            </div>
            <!-- /.col-md-6 -->
            <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                <h5 class="m-0">Featured</h5>
                </div>
                <div class="card-body">
                <h6 class="card-title">Special title treatment</h6>

                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-header">
                <h5 class="m-0">Featured</h5>
                </div>
                <div class="card-body">
                <h6 class="card-title">Special title treatment</h6>

                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section("script")
    //javascript code without tag <script></script>
@endsection';

        File::put($myfile, $txt);
    }

    private function createReadmePlugin($path, $description)
    {
        //creating README
        //create folder if doesn't exist
        $myfile = $path . "README.md";
        File::put($myfile, $description);
    }

    private function createInfoPlugin($path, $name, $description, $author, $email)
    {
        //creating info.json
        //create folder if doesn't exist
        $array = array(
            "name" => $name,
            "description" => $description,
            "author" => $author,
            "email" => $email,
        );

        $myfile = $path . "info.json";
        File::put($myfile, json_encode($array));
    }
}
