<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        return Voucher::all();
    }

    public function store(Request $request)
    {
        return Voucher::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update($request->all());
        return $voucher;
    }

    public function destroy($id)
    {
        Voucher::destroy($id);

        return response()->json(['message' => 'Voucher dihapus']);
    }

    public function check($kode)
    {
        $voucher = Voucher::where('kode', $kode)->first();

        if (!$voucher) {
            return ['valid' => false];
        }

        return [
            'valid' => true,
            'voucher' => $voucher
        ];
    }
}
