<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use Illuminate\Http\Request;

class KosController extends Controller
{
    // 1. TAMPILKAN KOS (Sudah ada isolasi Tenant!)
    public function index(Request $request)
    {
        $query = Kos::query();

        // 🔥 INI YANG BIKIN KOS TERISOLASI PER OWNER:
        if ($request->has('owner_id')) {
            $query->where('users_id', $request->input('owner_id'));
        }

        $kos = $query->get();

        return response()->json([
            'success' => true,
            'data' => $kos
        ], 200);
    }

    // 2. TAMBAH KOS BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'alamat'          => 'required|string',
            'jumlah_kamar'    => 'required|integer',
            'gender'          => 'required|string',
            'rating'          => 'nullable|numeric',
            'region_idregion' => 'required|integer',
            'peraturan'       => 'nullable|string',
            'fasilitas_umum'  => 'nullable|array',
        ]);

        $data = $request->all();
        // Pakai users_id sesuai database TiDB
        $data['users_id'] = $request->user() ? $request->user()->id : $request->input('owner_id');

        if ($request->has('fasilitas_umum')) {
            $data['fasilitas_umum'] = json_encode($request->input('fasilitas_umum'));
        }

        $kos = Kos::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil ditambahkan',
            'data' => $kos
        ], 201);
    }

    // 3. LIHAT DETAIL 1 KOS
    public function show($id)
    {
        $kos = Kos::find($id);
        if (!$kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $kos], 200);
    }

    // 4. EDIT KOS (Ini yang bikin error <!DOCTYPE tadi)
    public function update(Request $request, $id)
    {
        $kos = Kos::find($id);
        if (!$kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        $data = $request->all();

        // Handle array fasilitas
        if ($request->has('fasilitas_umum')) {
            $data['fasilitas_umum'] = is_array($request->fasilitas_umum)
                ? json_encode($request->fasilitas_umum)
                : $request->fasilitas_umum;
        }

        $kos->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil diperbarui!',
            'data' => $kos
        ], 200);
    }

    // 5. HAPUS KOS (Ini juga yang bikin error <!DOCTYPE)
    public function destroy($id)
    {
        $kos = Kos::find($id);
        if (!$kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        $kos->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil dihapus!'
        ], 200);
    }
}
