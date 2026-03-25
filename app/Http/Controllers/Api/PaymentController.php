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
            'rooms_id' => 'required|integer',
            'tenant' => 'required',
            'jenis_pembayaran' => 'required|in:bulanan,tahunan',
            'voucher_id' => 'nullable|integer',
            'amount'           => 'nullable|numeric',
        ]);

        $payment = Payment::create([
            'rooms_id' => $request->rooms_id,
            'tenant' => $request->tenant,
            'status' => 'UNPAID',
            'tanggal_bayar' => Carbon::now(),
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'voucher_id' => $request->voucher_id,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'message' => 'Payment record saved successfully',
            'data' => $payment,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $payment = Payment::with(['room', 'user'])->find($id);
        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $payment], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function pay($id)
    {
        $payment = Payment::find($id);
        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan'], 404);
        }

        $payment->update([
            'status' => 'PAID',
            'tanggal_bayar' => Carbon::now(),
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
    public function seePayment(Request $request)
{
    // Ambil tenant dari request (misal dikirim: { "tenant": 160423046 })
    $tenantId = $request->tenant;

    if (!$tenantId) {
        return response()->json(['success' => false, 'message' => 'Tenant ID diperlukan'], 400);
    }

    $payments = Payment::with(['room', 'user'])
        ->where('tenant', $tenantId)
        // Prioritas UNPAID di atas (1), baru PAID (2)
        ->orderByRaw("CASE WHEN status = 'UNPAID' THEN 1 ELSE 2 END")
        ->orderBy('updated_at', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $payments
    ], 200);
}
}
