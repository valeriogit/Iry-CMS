<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Configuration;
use App\Models\MenuList;
use App\Models\Role;

class MenuController extends Controller
{
    private $config;
    protected $MENU = "menu";
    protected $MANAGEMENU = "manageMenu";

    public function __construct()
    {
        $this->config = Configuration::first();
        $this->activePage = $this->MENU;
    }

    public function createMenu(){
        return view('backend.createMenu')
            ->with('config', $this->config)
            ->with('activePage', $this->activePage);
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

    public function showMenu(){

        $menuList = MenuList::where('side', '=', 'frontend')->get();
        return view('backend.showMenu')
            ->with('config', $this->config)
            ->with('activePage', $this->MANAGEMENU)
            ->with('menuList', $menuList);
    }

    public function changeVisibility(Request $request){
        $validated = $request->validate([
            'menu'  => 'required|max:250'
        ]);

        try {
            $menu = MenuList::where('id', '=', $request->menu)->first();
            if($menu){
                $menu->visible = 1;
                $menu->save();

                MenuList::where('id', '<>', $request->menu)->where('side', '=', 'frontend')->update(['visible' => 0]);

                return true;
            }
        } catch (\Exception $e) {
            //dd($e);
            return false;
        }

        return false;
    }

    public function modifyMenu($id){

        $menu = MenuList::find($id);
        return view('backend.modifyMenu')
            ->with('config', $this->config)
            ->with('activePage', $this->MANAGEMENU)
            ->with('menu', $menu);
    }

    public function updateMenu(Request $request, $id)
    {
        $validated = $request->validate([
            'name'  => 'required|max:250',
            'menu'  => 'required'
        ]);

        try {
            $menu = MenuList::where('name', '=', $request->name)
                ->where('id', '<>', $id)
                ->first();
            if($menu){
                return false;
            }

            $menu = MenuList::find($id);

            $menu->name = $request->name;
            $menu->param = $request->menu;

            $menu->save();
        } catch (\Exception $e) {
            //dd($e);
            return false;
        }

        return true;
    }

    public function deleteMenu($id){
        try {
            $menu = MenuList::find($id);

            if($menu->visible == 1){
                session()->flash('deletedFailMenu', 'deleted');
                return redirect()->action([MenuController::class, 'showMenu']);
            }

            MenuList::destroy($id);
            session()->flash('deletedSuccessMenu', 'deleted');
            return redirect()->action([MenuController::class, 'showMenu']);

        } catch (\Exception $e) {
            session()->flash('deletedFailMenu', 'deleted');
            return redirect()->action([MenuController::class, 'showMenu']);
        }
    }
}
