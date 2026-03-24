<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        //
        return response()->json([
            'success' => true,
            'data'    => $request->user()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'no_hp'    => 'sometimes|required|string|min:10|max:20|unique:users,no_hp,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('name')) {
            $user->nama = $request->name;
        }
        if ($request->has('no_hp')) {
            $user->no_hp = $request->no_hp;
        }
        if ($request->has('password') && $request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diupdate!',
            'data'    => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
