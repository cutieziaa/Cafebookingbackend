<?php

namespace App\Http\Controllers;

use App\Models\Pickup;
use Illuminate\Http\Request;

class PickupController extends Controller
{
    public function store(Request $request)
    {
        $pickup = Pickup::create([
            'user_id' => $request->user()->id,
            'nama_penerima' => $request->nama_penerima,
            'catatan' => $request->catatan,
        ]);

        return $pickup;
    }

    public function myPickup(Request $request)
    {
        return Pickup::where('user_id', $request->user()->id)->get();
    }

    public function updateStatus(Request $request, $id)
    {
        $pickup = Pickup::findOrFail($id);
        $pickup->update([
            'status' => $request->status
        ]);

        return $pickup;
    }
}
