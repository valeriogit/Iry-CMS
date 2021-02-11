<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\Role;

class Post extends Model
{
    use HasFactory;

    public static function editPost($post){
        $role = Role::find(Auth::user()->role);

        if($role->editPost == 1 && $post->user_id == Auth::user()->id){
            if($post->status < 2 ){
                return true;
            }

            if($post->status == 2 && $role->editPublishedPost == 1){
                return true;
            }
        }

        if($role->editPost == 1 && $role->editOther==1){
            return true;
        }

        abort(401);
    }

    public static function canUploadFile(){
        $role = Role::find(Auth::user()->role);

        if($role->uploadFiles == 1){
            return true;
        }

        return false;
    }
}
