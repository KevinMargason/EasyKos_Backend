<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $payments = Payment::with(['room', 'user'])->get();
        return response()->json(['success' => true, 'data' => $payments], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'rooms_id'         => 'required|integer',
            'tenant'           => 'required|integer',
            'jenis_pembayaran' => 'required|in:bulanan,tahunan',
            'voucher_id'       => 'required|integer'
        ]);

        $payment = Payment::create([
            'rooms_id'         => $request->rooms_id,
            'tenant'           => $request->tenant,
            'status'           => 'UNPAID',
            'tanggal_bayar'    => Carbon::now(),
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'voucher_id'       => $request->voucher_id
        ]);

        return response()->json(['success' => true, 'message' => 'Tagihan berhasil dibuat', 'data' => $payment], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $payment = Payment::with(['room', 'user'])->find($id);
        if (!$payment) return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $payment], 200);
    }

    /**
     * Update the specified resource in storage.
     */

    public function pay($id)
    {
        $payment = Payment::find($id);
        if (!$payment) return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan'], 404);

        $payment->update([
            'status' => 'PAID',
            'tanggal_bayar' => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil, status menjadi PAID', 'data' => $payment], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
