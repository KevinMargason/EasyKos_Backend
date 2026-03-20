<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(['success' => true, 'data' => Voucher::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nominal_diskon' => 'required|integer',
            'expired'        => 'required|date'
        ]);
        $voucher = Voucher::create($request->all());
        return response()->json(['success' => true, 'data' => $voucher], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $voucher = Voucher::find($id);
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Voucher tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $voucher], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $voucher = Voucher::find($id);
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Voucher tidak ditemukan'], 404);
        $voucher->update($request->all());
        return response()->json(['success' => true, 'message' => 'Voucher diupdate', 'data' => $voucher], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $voucher = Voucher::find($id);
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Voucher tidak ditemukan'], 404);
        $voucher->delete();
        return response()->json(['success' => true, 'message' => 'Voucher dihapus'], 200);
    }
}
