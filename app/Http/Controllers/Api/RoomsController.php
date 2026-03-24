<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rooms;
use Illuminate\Http\Request;

class RoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $rooms = Rooms::with('kos')->get();
        return response()->json(['success' => true, 'data' => $rooms], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'kos_id'       => 'required|integer',
            'harga'        => 'required|integer',
            'nomor_kamar'  => 'required|string|max:45',
            'ukuran_kamar' => 'required|string|max:45',
            'listrik'      => 'required|in:token,pasca_bayar,tidak_ada',
            'users_id'     => 'required|integer'
        ]);

        $room = Rooms::create($request->all());
        return response()->json(['success' => true, 'message' => 'Kamar berhasil ditambahkan', 'data' => $room], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $room = Rooms::with('kos')->find($id);
        if (!$room) return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $room], 200);
    }

    public function getRoomsByKos($kosId)
    {
        $rooms = Rooms::where('kos_id', $kosId)->with('kos')->get();
        return response()->json(['success' => true, 'data' => $rooms], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $room = Rooms::find($id);
        if (!$room) return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);

        $room->update($request->all());
        return response()->json(['success' => true, 'message' => 'Kamar berhasil diupdate', 'data' => $room], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $room = Rooms::find($id);
        if (!$room) return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);

        $room->delete();
        return response()->json(['success' => true, 'message' => 'Kamar berhasil dihapus'], 200);
    }
}
