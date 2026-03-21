<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MyTupai;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyTupaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $tupai = MyTupai::with('user')->get();
        return response()->json(['success' => true, 'data' => $tupai], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'nama'     => 'required|string|max:45',
            'users_id' => 'required|integer'
        ]);

        $existingTupai = MyTupai::where('users_id', $request->users_id)->first();
        if ($existingTupai) {
            return response()->json(['success' => false, 'message' => 'User ini sudah memiliki Tupai!'], 400);
        }

        $tupai = MyTupai::create([
            'nama'           => $request->nama,
            'level'          => '1',
            'xp'             => '0',
            'level_lapar'    => 100,
            'level_stamina'  => 100,
            'status'         => 'normal',
            'terakhir_makan' => Carbon::now(),
            'terakhir_tidur' => Carbon::now(),
            'tidur_sampai'   => Carbon::now(),
            'users_id'       => $request->users_id
        ]);

        return response()->json(['success' => true, 'message' => 'Tupai berhasil diadopsi!', 'data' => $tupai], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $tupai = MyTupai::find($id);
        if (!$tupai) return response()->json(['success' => false, 'message' => 'Tupai tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $tupai], 200);
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
