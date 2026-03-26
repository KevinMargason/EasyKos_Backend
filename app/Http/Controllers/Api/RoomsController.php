<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomsController extends Controller
{
    private function resolveOwnerId(Request $request): int|string|null
    {
        return $request->user()?->id ?? $request->input('users_id') ?? $request->input('owner_id');
    }

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
        $payload = $request->all();
        $ownerId = $this->resolveOwnerId($request);

        if ($ownerId !== null && $ownerId !== '') {
            $payload['users_id'] = $ownerId;
        }

        $validator = Validator::make($payload, [
            'kos_id' => 'required',
            'harga' => 'required',
            'jumlah_kamar_loop' => 'required', // Kita longgarkan sedikit biar nggak error kena FormData
            'ukuran_kamar' => 'required',
            'listrik' => 'required',
            'users_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors(),
            ], 422); // Memaksa return JSON error 422
        }

        $baseData = $validator->validated();
        unset($baseData['jumlah_kamar_loop']);

        // 1. Amankan Fasilitas
        if ($request->has('fasilitas_kamar')) {
            $baseData['fasilitas_kamar'] = json_encode($request->fasilitas_kamar);
        }

        // 2. Amankan Foto
        if ($request->hasFile('foto')) {
            $fotoPaths = [];
            foreach ($request->file('foto') as $file) {
                $path = $file->store('rooms', 'public');
                $fotoPaths[] = $path;
            }
            $baseData['foto'] = json_encode($fotoPaths);
        }

        // 3. JURUS CLONE KAMAR (Looping)
        
        $jumlahKamar = $request->jumlah_kamar_loop;
        $createdRooms = [];

        for ($i = 1; $i <= $jumlahKamar; $i++) {
            $data = $baseData;

            // Bikin nama kamar otomatis: Kamar 01, Kamar 02, dst.
            $data['nomor_kamar'] = 'Kamar '.str_pad($i, 2, '0', STR_PAD_LEFT);

            /**
             * LOGIKA BARU:
             * Jika ini BUKAN kamar pertama ($i > 1), maka kita set users_id menjadi null.
             * Kamar pertama ($i == 1) tetap menggunakan users_id dari request.
             */
            if ($i > 1) {
                $data['users_id'] = null;
            }

            $createdRooms[] = Rooms::create($data);

            return response()->json([
                'success' => true,
                'message' => "$jumlahKamar Kamar & Foto berhasil ditambahkan!",
                'data' => $createdRooms,
            ], 201);
    
        
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $room = Rooms::with('kos')->find($id);
        if (! $room) {
            return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);
        }

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
        if (! $room) {
            return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);
        }

        $data = $request->except(['foto', '_method']);

        if ($request->has('fasilitas_kamar')) {
            $data['fasilitas_kamar'] = is_array($request->fasilitas_kamar)
                ? json_encode($request->fasilitas_kamar)
                : $request->fasilitas_kamar;
        }

        if ($request->hasFile('foto')) {
            $fotoPaths = [];
            foreach ($request->file('foto') as $file) {
                $path = $file->store('rooms', 'public');
                $fotoPaths[] = $path;
            }
            $data['foto'] = json_encode($fotoPaths);
        }

        $room->update($data);

        return response()->json(['success' => true, 'message' => 'Kamar & Foto berhasil diupdate', 'data' => $room], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $room = Rooms::find($id);
        if (! $room) {
            return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);
        }

        $room->delete();

        return response()->json(['success' => true, 'message' => 'Kamar berhasil dihapus'], 200);
    }

    public function updateOwner(Request $request, string $id)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,id', // Pastikan ID user baru ada di tabel users
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 2. Cari kamarnya
        $room = Rooms::find($id);
        if (! $room) {
            return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);
        }

        // 3. Update hanya users_id
        $room->update([
            'users_id' => $request->users_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemilik kamar berhasil diperbarui',
            'data' => $room->load('kos'), // Load relasi biar data yang dikirim lengkap
        ], 200);
    }
}
