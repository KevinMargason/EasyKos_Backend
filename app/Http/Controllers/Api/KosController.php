<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $kos = Kos::all();
        return response()->json(['success' => true, 'data' => $kos], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nama'            => 'required|string|max:255',
            'alamat'          => 'required|string',
            'jumlah_kamar'    => 'required|integer',
            'gender'          => 'required|string',
            'rating'          => 'nullable|numeric',
            'region_idregion' => 'required|integer'
        ]);

        $kos = Kos::create($request->all());
        return response()->json(['success' => true, 'message' => 'Kos berhasil ditambahkan', 'data' => $kos], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $kos = Kos::with(['aturans', 'fasilitas', 'owner'])->find($id);
        if (!$kos) return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);

        // fallback: if owner relation is absent (owner_id not set), attempt to find via users table with role owner
        if (!$kos->owner && isset($kos->owner_id) && $kos->owner_id) {
            $kos->owner = User::find($kos->owner_id);
        }

        return response()->json(['success' => true, 'data' => $kos], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $kos = Kos::find($id);
        if (!$kos) return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);

        $kos->update($request->all());
        return response()->json(['success' => true, 'message' => 'Kos berhasil diupdate', 'data' => $kos], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $kos = Kos::find($id);
        if (!$kos) return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);

        $kos->delete();
        return response()->json(['success' => true, 'message' => 'Kos berhasil dihapus'], 200);
    }

    public function currentKos(Request $request)
    {
        $user = $request->user();

        $room = DB::table('rooms')->where('users_id', $user->id)->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum menyewa kamar kos apapun.'
            ], 404);
        }

        $kos = DB::table('kos')->where('id', $room->kos_id)->first();

        return response()->json([
            'success' => true,
            'data'    => $kos
        ], 200);
    }

    public function allResidents()
    {
        $residents = DB::table('users')
            ->select('id', 'name', 'email', 'no_hp', 'role')
            ->where('role', 'tenant')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $residents
        ], 200);
    }

    public function kosResidents($kosId)
    {
        $userIds = DB::table('rooms')
            ->where('kos_id', $kosId)
            ->pluck('users_id');

        // Ambil data profil mereka
        $residents = DB::table('users')
            ->select('id', 'name', 'email', 'no_hp', 'role')
            ->whereIn('id', $userIds)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $residents
        ], 200);
    }
}
