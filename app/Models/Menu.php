<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\Role;

class Menu extends Model
{
    use HasFactory;

    protected $table = "menu";

    public static function getMenuBackend()
    {
        $menu = DB::table('menu')
            ->select('menuvoice.name', 'menuvoice.url', 'menuvoice.slug', 'menuvoice.icon', 'menuvoice.roles')
            ->join('menuvoice', 'menu.idMenuVoice', '=', 'menuvoice.id')
            ->join('menulist', 'menu.idMenuList', 'menulist.id')
            ->where('menulist.side', '=', 'backend')
            ->where('menulist.visible', '=', '1')
            ->get();

        return $menu;
    }
}
