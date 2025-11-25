<?php

namespace App\Http\Controllers;

use App\Models\MejaTipe;
use Illuminate\Http\Request;

class MejaTipeController extends Controller
{
    public function index()
    {
        return MejaTipe::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50'
        ]);

        return MejaTipe::create([
            'nama' => $request->nama
        ]);
    }
}
