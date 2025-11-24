<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return Menu::all();
    }

    public function show($id)
    {
        return Menu::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama' => 'required',
            'harga' => 'required',
        ]);

        return Menu::create($validate);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($request->all());
        return $menu;
    }

    public function destroy($id)
    {
        Menu::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
