<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $methods = PaymentMethod::all();
        return response()->json([
            'success' => true,
            'data'    => $methods
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'nama_metode' => 'required|string|max:255',
            'tipe'      => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:255',
            'atas_nama' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $method = PaymentMethod::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil ditambahkan!',
            'data'    => $method
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $method = PaymentMethod::find($id);

        if (!$method) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran tidak ditemukan!'
            ], 404);
        }

        $method->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil diupdate!',
            'data'    => $method
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $method = PaymentMethod::find($id);

        if (!$method) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran tidak ditemukan!'
            ], 404);
        }

        $method->delete();

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil dihapus!'
        ], 200);
    }
}
