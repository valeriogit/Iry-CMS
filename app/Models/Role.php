<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    use HasFactory;

    //$roles is an array
    public static function checkRole($roles){
        if(Auth::check()){
            foreach ($roles as $role) {
                $role = Role::where('name', '=', $role)->first();
                if($role->id == Auth::user()->role){
                    return true;
                }
            }
        }
        return false;
    }

    //$roles is an array
    public static function checkRoleId($roles){
        if(Auth::check()){
            foreach ($roles as $role) {
                if($role == Auth::user()->role){
                    return true;
                }
            }
        }
        return false;
    }

    public static function checkRoleMenu($rolesMenu){
        $rolesMenu = explode(",",$rolesMenu);
        return self::checkRoleId($rolesMenu);
    }
}
