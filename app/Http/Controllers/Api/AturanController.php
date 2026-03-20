<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aturan;
use Illuminate\Http\Request;

class AturanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(['success' => true, 'data' => Aturan::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nama_aturan' => 'required|string|max:100',
            'status'      => 'required|boolean'
        ]);
        $aturan = Aturan::create($request->all());
        return response()->json(['success' => true, 'data' => $aturan], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $aturan = Aturan::find($id);
        if (!$aturan) return response()->json(['success' => false, 'message' => 'Aturan tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $aturan], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $aturan = Aturan::find($id);
        if (!$aturan) return response()->json(['success' => false, 'message' => 'Aturan tidak ditemukan'], 404);

        $aturan->update($request->all());
        return response()->json(['success' => true, 'message' => 'Aturan berhasil diupdate', 'data' => $aturan], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $aturan = Aturan::find($id);
        if (!$aturan) return response()->json(['success' => false, 'message' => 'Aturan tidak ditemukan'], 404);

        $aturan->delete();
        return response()->json(['success' => true, 'message' => 'Aturan berhasil dihapus'], 200);
    }
}
