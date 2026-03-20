<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(['success' => true, 'data' => Fasilitas::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nama_fasilitas' => 'required|string|max:100',
            'kategori'       => 'required|in:privat,publik',
            'status'         => 'required|boolean'
        ]);
        $fasilitas = Fasilitas::create($request->all());
        return response()->json(['success' => true, 'data' => $fasilitas], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $fasilitas = Fasilitas::find($id);
        if (!$fasilitas) return response()->json(['success' => false, 'message' => 'Fasilitas tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $fasilitas], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $fasilitas = Fasilitas::find($id);
        if (!$fasilitas) return response()->json(['success' => false, 'message' => 'Fasilitas tidak ditemukan'], 404);

        $fasilitas->update($request->all());
        return response()->json(['success' => true, 'message' => 'Fasilitas berhasil diupdate', 'data' => $fasilitas], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $fasilitas = Fasilitas::find($id);
        if (!$fasilitas) return response()->json(['success' => false, 'message' => 'Fasilitas tidak ditemukan'], 404);

        $fasilitas->delete();
        return response()->json(['success' => true, 'message' => 'Fasilitas berhasil dihapus'], 200);
    }
}
