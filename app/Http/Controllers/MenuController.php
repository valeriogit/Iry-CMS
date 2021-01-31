<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Configuration;
use App\Models\MenuList;
use App\Models\Role;

class MenuController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = Configuration::first();
        $this->activePage = "menu";
    }

    public function createMenu(){

        $roles = Role::all();

        return view('backend.createMenu')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage)
            ->with('roles', $roles);
    }

    public function saveMenu(Request $request){
        $validated = $request->validate([
            'name'  => 'required|max:250',
            'menu'  => 'required'
        ]);

        try {
            $menu = MenuList::where('name', '=', $request->name)->first();
            if($menu){
                return false;
            }

            $menu = new MenuList;

            $menu->name = $request->name;
            $menu->side = "frontend";
            $menu->param = $request->menu;

            $menu->save();
        } catch (\Exception $e) {
            //dd($e);
            return false;
        }

        return true;
    }

    public function checkNameMenu(Request $request){
        $validated = $request->validate([
            'name'  => 'required|max:250'
        ]);

        try {
            $menu = MenuList::where('name', '=', $request->name)->first();
            if($menu){
                return true;
            }

        } catch (\Exception $e) {
            //dd($e);
            return false;
        }

        return false;
    }
}
