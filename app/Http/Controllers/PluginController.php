<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use ZipArchive;

use App\Models\Configuration;
use App\Models\Plugin;
use App\Models\Menu;
use App\Models\MenuList;
use App\Models\MenuVoice;
use App\Models\ReCaptcha;

class PluginController extends Controller
{
    public function pluginPage($page, $activePage = 'admin')
    {
        //we call this funciont from plugin for make views
        //and we call in this controller too
        $config = Configuration::first();

        return View::make($page)
            ->with('config', $config)
            ->with('activePage', $activePage);
    }

    public function show()
    {
        if(!session()->has('downloadPlugin')){
            $path = public_path('tmp/');
            if (file_exists($path)) {
                File::cleanDirectory($path);
            }
        }

        $plugins = Plugin::all();

        return $this->pluginPage('backend.showPlugins', 'plugin')
            ->with('plugins', $plugins);
    }

    public function create()
    {
        if(!session()->has('downloadPlugin')){
            $path = public_path('tmp/');
            if (file_exists($path)) {
                File::cleanDirectory($path);
            }
        }

        return $this->pluginPage('backend.createPlugin', 'plugin');
    }

    public function upload()
    {
        return $this->pluginPage('backend.uploadPlugin', 'plugin');
    }

    public function uploaded(Request $request)
    {
        $validated = $request->validate([
            'zip' => 'required|file|mimes:zip'
        ]);

        $captcha = ReCaptcha::checkReCaptcha($request);
        if($captcha === false){
            session()->flash('errorPlugin', 'An error occurred, retry please!');
            return back();
        }

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
                            $strJsonFileContents = File::get($pathPlugins . "/info.json");
                            // Convert to array
                            $array = json_decode($strJsonFileContents, true);

                            $plugin->name = $array["name"];
                            $plugin->description = $array["description"];
                            $plugin->author = $array["author"];
                            $plugin->author_email = $array["email"];

                            $menuList = MenuList::find(1);

                            $menuVoice = new MenuVoice;
                            $menuVoice->name = $array["menuVoice"];
                            $menuVoice->url = $array["menuLink"];
                            $menuVoice->icon = $array["menuIcon"];
                            $menuVoice->slug = $array["author"]."_".$array["name"];
                            $menuVoice->save();

                            $menu = new Menu;
                            $menu->idMenuList = $menuList->id;
                            $menu->idMenuVoice = $menuVoice->id;
                            $menu->save();
                        }
                        else{
                            $plugin->name = $file_name;
                            $plugin->author = $author;
                        }

                        $plugin->save();

                        session()->flash('installedPlugin', 'installed');
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

    public function delete($id){
        try{
            $plugin = Plugin::find($id);

            if($plugin){
                $path = app_path('Http\Plugins\\'.$plugin->author.'\\'.$plugin->name);
                if (file_exists($path)) {
                    File::deleteDirectory($path);

                    $path = app_path('Http\Plugins\\'.$plugin->author);

                    if ($this->is_dir_empty($path)) {
                        //the folder is empty"
                        File::deleteDirectory($path);
                    }
                }

                $this->removeFromFile($plugin->author, $plugin->name, $plugin->id);
                $plugin->delete();

                $menuList = MenuList::find(1);

                $menuVoice = MenuVoice::where('slug', '=', $plugin->author.'_'.$plugin->name)->first();
                $menu = Menu::where('idMenuVoice', '=', $menuVoice->id)->delete();
                $menuVoice->delete();

                session()->flash('deletedSuccessPlugin', 'deleted');
                return redirect()->action([PluginController::class, 'show']);

            }

            session()->flash('deletedFailPlugin', 'deleted');
            return redirect()->action([PluginController::class, 'show']);

        }catch(\Exception $e){
            //dd($e);
        }
    }

    private function is_dir_empty($dir) {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }

    private function removeFromFile($author, $pluginName, $pluginId){
        $provider = file(app_path('Http\Plugins\PluginProvider.php'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $flag = false;
        $positionInsert = "";

        foreach ($provider as $key => $value) {
            if ($value == '        $calls->' . $pluginName . '();') {
                $positionInsert = $key;
            }
        }

        if ($positionInsert != "") {
            unset($provider[$positionInsert]);
        }

        $plugins = Plugin::where('author', '=', $author)->where('id','<>', $pluginId)->first();

        if(!$plugins){
            $positionInsert = "";
            foreach ($provider as $key => $value) {
                if ($value == '                app_path("\Http\Plugins\\\\' . $author . '\\\\" . $name  . "\\\\routes\web.php"),') {
                    $positionInsert = $key;
                }
            }

            if ($positionInsert != "") {
                unset($provider[$positionInsert]);
            }
        }

        $myfile = fopen(app_path('Http\Plugins\PluginProvider.php'), "w");
        $provider = implode("\n", $provider);
        $txt = $provider;
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    public function downloadZip($id){
        $plugin = Plugin::find($id);

        $path = app_path('Http/Plugins/' . $plugin->author.'/');
        $this->zipPlugin($path, $plugin->author, $plugin->name);

        $file_name = $plugin->author."_".$plugin->name.".zip";
        $dest = public_path('tmp/'.$file_name);
        File::move($path.$file_name, $dest);

        session()->flash('downloadPlugin', $file_name   );
        return redirect()->action([PluginController::class, 'show']);
    }

    public function modify(Request $request, $id)
    {
        try{
            $plugin = Plugin::find($id);

            $file = $plugin->name.".php";
            $path = app_path('Http\Plugins\\');

            if ($request->has('file')) {
                $file = $request->file;
                $content = File::get($path.$file);
            }
            else{
                $path = app_path('Http\Plugins\\'.$plugin->author.'\\'.$plugin->name);
                $content = File::get($path.'\\'.$file);
            }

            $path = app_path('Http\Plugins\\'.$plugin->author.'\\'.$plugin->name);

            //$content = Blade::compileString($content);

            //$code = $this->php_file_tree($path, "javascript:alert('You clicked on [link]');");
            $allowed_extensions = array("php", "css", "js", "json", "md", "html");
            $code = $this->php_file_tree($path, "?file=[link]", $allowed_extensions);

            return $this->pluginPage('backend.modifyPlugin', 'plugin')
                ->with('content', $content)
                ->with('code', $code)
                ->with('id', $plugin->id);
        }
        catch(\Exception $e){
            //dd($e);
            return back();
        }
    }

    private function php_file_tree($directory, $return_link, $extensions = array()) {
        // Generates a valid XHTML list of all directories, sub-directories, and files in $directory
        // Remove trailing slash
        if( substr($directory, -1) == "/" ) $directory = substr($directory, 0, strlen($directory) - 1);
        $code = $this->php_file_tree_dir($directory, $return_link, $extensions);

        return $code;
    }

    private function php_file_tree_dir($directory, $return_link, $extensions = array(), $first_call = true) {
        // Recursive function called by php_file_tree() to list directories/files
        $php_file_tree = "";
        // Get and sort directories/files
        $file = scandir($directory);
        natcasesort($file);
        // Make directories first
        $files = $dirs = array();
        foreach($file as $this_file) {
            if( is_dir("$directory/$this_file" ) ) $dirs[] = $this_file; else $files[] = $this_file;
        }
        $file = array_merge($dirs, $files);

        // Filter unwanted extensions
        if( !empty($extensions) ) {
            foreach( array_keys($file) as $key ) {
                if( !is_dir("$directory/$file[$key]") ) {
                    $ext = substr($file[$key], strrpos($file[$key], ".") + 1);
                    if( !in_array($ext, $extensions) ) unset($file[$key]);
                }
            }
        }

        if( count($file) > 2 ) { // Use 2 instead of 0 to account for . and .. "directories"
            $php_file_tree = "<ul";
            if( $first_call ) { $php_file_tree .= " class=\"php-file-tree\""; $first_call = false; }
            $php_file_tree .= ">";
            foreach( $file as $this_file ) {
                if( $this_file != "." && $this_file != ".." ) {
                    if( is_dir("$directory/$this_file") ) {
                        // Directory
                        $php_file_tree .= "<li class=\"pft-directory\"><a href=\"#\">" . htmlspecialchars($this_file) . "</a>";
                        $php_file_tree .= $this->php_file_tree_dir("$directory/$this_file", $return_link ,$extensions, false);
                        $php_file_tree .= "</li>";
                    } else {
                        // File
                        // Get extension (prepend 'ext-' to prevent invalid classes from extensions that begin with numbers)
                        $ext = "ext-" . substr($this_file, strrpos($this_file, ".") + 1);
                        $link = str_replace("[link]", "$directory/" . urlencode($this_file), $return_link);
                        $link = str_replace(app_path('Http\Plugins\\'), "", $link);
                        $php_file_tree .= "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"$link\">" . htmlspecialchars($this_file) . "</a></li>";
                    }
                }
            }
            $php_file_tree .= "</ul>";
        }
        return $php_file_tree;
    }

    public function saveModify(Request $request, $id)
    {
        if($request->has('file') && $request->has('content')){
            try{
                $file = $request->file;
                $content = $request->content;

                $plugin = Plugin::find($id);

                if($file == null || $file == ""){
                    $file = $plugin->author.'\\'.$plugin->name.'\\'.$plugin->name.".php";
                }

                $path = app_path('Http\Plugins\\');
                File::put($path.$file, $content);

                return response()->json(['code' => 'success', 'state' => 'Success', 'message' => 'Successfully saved']);
            }
            catch (\Exception $e){
                dd($e);
                return response()->json(['code' => 'error', 'state' => 'Fail', 'message' => 'Save failed']);
            }
        }

        return response()->json(['code' => 'error', 'state' => 'Fail', 'message' => 'Save failed']);
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
            }

            //funzioni che creano i diversi file
            $this->createModelPlugin($path, $author, $name);
            $this->createControllerPlugin($path, $author, $name);
            $this->createRoutePlugin($path, $author, $name);
            $this->createViewPlugin($path, $author, $name);
            $this->createAssetsPlugin($path);
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
        return PluginController::pluginPage("' . $author . '\\' . $name . '\\\views\\\" . $page, "' . $author . '_' . $name . '");
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

//If you want a frontend link remove /admin

Route::get('/admin/" . $author . '/' . $name . "/', [Controller" . $name . "::class, 'index']);";

        File::put($myfile, $txt);
    }

    private function createViewPlugin($path, $author, $name)
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

@section(\'title\')
    <title>{{ $config->nameSite }} - Title Page</title>
@endsection

@section(\'css\')
    <!-- Your css files -->
    <link rel="stylesheet" href="{{ URL::to(\'/assets/'.$author.'/'.$name.'/css/main.css\') }}">
@endsection

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

@section(\'js\')
    <!-- Your JS files -->
    <script src="{{ URL::to(\'/assets/'.$author.'/'.$name.'/js/main.js\') }}"></script>
@endsection

@section("script")
    <script>
        //javascript code
    </script>
@endsection';

        File::put($myfile, $txt);
    }

    private function createAssetsPlugin($path)
    {
        //creating views
        //create folder if doesn't exist
        $path = $path.'resources/';
        if (!file_exists($path)) {
            File::makeDirectory($path);
        }

        $cssPath = $path . "css/";
        if (!file_exists($cssPath)) {
            File::makeDirectory($cssPath);
        }

        //create file and insert text
        $myfile = $cssPath . "main.css";
        File::put($myfile, '');

        $jsPath = $path . "js/";
        if (!file_exists($jsPath)) {
            File::makeDirectory($jsPath);
        }

        //create file and insert text
        $myfile = $jsPath . "main.js";
        File::put($myfile, '');
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
            "menuVoice" => $name,
            "menuLink" => '/admin/' . $author . '/' . $name . '/',
            "menuIcon" => "fas fa-bars"
        );

        $myfile = $path . "info.json";
        File::put($myfile, json_encode($array));
    }
}
