<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Configuration;
use App\Models\Media;
use App\Models\Post;
use App\Models\Category;

class PostController extends Controller
{
    protected $POST = "post";
    protected $MANAGEPOST = "managePost";

    public function __construct(){
        $this->config = Configuration::first();
        $this->activePage = $this->POST;
    }

    public function createPost(){
        $media = Media::limit(12)->get();

        $media = Media::paginate(12);
        $totMedia = $media->lastPage();
        $categories = Category::all();

        return view('backend.createPost')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage)
            ->with('media', $media)
            ->with('totMedia', $totMedia)
            ->with('categories', $categories);
    }

    public function uploadFile(Request $request){
        $validated = $request->validate([
            'file' => 'required|file'
        ]);

        if(!$request->file->isValid()) {
            return response()->json( "error" ,403);
        }

        if(Post::canUploadFile()){

            $path = public_path('upload');

            $nameFile = time().".".$request->file->getClientOriginalName();

            $request->file->move($path, $nameFile);

            $media = new Media;

            $media->user_id = Auth::user()->id;
            $media->name = $nameFile;
            $media->path = 'upload/'.$nameFile;
            $media->type = $request->file->getClientMimeType();

            $media->save();

            $file = array(
                "name" => $media->name,
                "path" => $media->path,
                "type" => $media->type
            );

            return response()->json( $file ,200);
        }
    }

    public function listFile(Request $request){
        $validated = $request->validate([
            'page' => 'required'
        ]);

        if($request->page == 1){
            $media = Media::select('name', 'path', 'type')->limit(12)->get();
        }else{
            $skip = ($request->page -1) * 12;
            $media = Media::select('name', 'path', 'type')->skip($skip)->limit(12)->get();
        }

        return response()->json( $media ,200);
    }

    public function checkTitlePost(Request $request){
        $validated = $request->validate([
            'title' => 'required|max:255'
        ]);

        $found = Post::where('slug', '=', $request->title)->count();

        return $found;
    }
}
