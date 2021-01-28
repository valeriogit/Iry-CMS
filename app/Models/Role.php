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

    //controlla i ruoli di un menu
    public static function checkRoleMenu($rolesMenu){
        $rolesMenu = explode(",",$rolesMenu);
        return self::checkRoleId($rolesMenu);
    }

    //prende il nome del ruolo di uno specifico utente
    public static function getNameUserRole($idRole){
        $role = Role::find($idRole);

        if($role){
            return $role->name;
        }

        return "";
    }
}
