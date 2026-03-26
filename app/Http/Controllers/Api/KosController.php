<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KosController extends Controller
{
    private function resolveOwnerId(Request $request): int|string|null
    {
        return $request->user()?->id
            ?? $request->query('users_id')
            ?? $request->query('owner_id')
            ?? $request->input('users_id')
            ?? $request->input('owner_id');
    }

    // 1. TAMPILKAN KOS (Sudah ada isolasi Tenant!)
    public function index(Request $request)
    {
        $query = Kos::query();

        // 🔥 INI YANG BIKIN KOS TERISOLASI PER OWNER:
        $ownerId = $request->query('users_id', $request->query('owner_id'));
        if ($ownerId !== null && $ownerId !== '') {
            $query->where('users_id', $ownerId);
        }

        $kos = $query->get();

        return response()->json([
            'success' => true,
            'data' => $kos,
        ], 200);
    }

    // 2. TAMBAH KOS BARU
    public function store(Request $request)
    {
        $payload = $request->all();
        $ownerId = $request->user()?->id ?? $request->input('users_id') ?? $request->input('owner_id');

        if ($ownerId !== null && $ownerId !== '') {
            $payload['users_id'] = $ownerId;
        }

        $validator = Validator::make($payload, [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jumlah_kamar' => 'required|integer',
            'gender' => 'required|string',
            'rating' => 'nullable|numeric',
            'region_idregion' => 'required|integer',
            'peraturan' => 'nullable|string',
            'fasilitas_umum' => 'nullable|array',
            'users_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['users_id'] = (int) $ownerId;

        if ($request->has('fasilitas_umum')) {
            $data['fasilitas_umum'] = json_encode($request->input('fasilitas_umum'));
        }

        $kos = Kos::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil ditambahkan',
            'data' => $kos,
        ], 201);
    }

    // 3. LIHAT DETAIL 1 KOS
    public function show($id)
    {
        $kos = Kos::find($id);
        if (! $kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $kos], 200);
    }

    // 4. EDIT KOS (Ini yang bikin error <!DOCTYPE tadi)
    public function update(Request $request, $id)
    {
        $kos = Kos::find($id);
        if (! $kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        $payload = $request->all();
        $ownerId = $request->user()?->id ?? $request->input('users_id') ?? $request->input('owner_id');

        if ($ownerId !== null && $ownerId !== '') {
            $payload['users_id'] = $ownerId;
        }

        $validator = Validator::make($payload, [
            'nama' => 'sometimes|string|max:255',
            'alamat' => 'sometimes|string',
            'jumlah_kamar' => 'sometimes|integer',
            'gender' => 'sometimes|string',
            'rating' => 'nullable|numeric',
            'region_idregion' => 'sometimes|integer',
            'peraturan' => 'nullable|string',
            'fasilitas_umum' => 'nullable|array',
            'users_id' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

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
            'data' => $kos,
        ], 200);
    }

    // 5. HAPUS KOS (Ini juga yang bikin error <!DOCTYPE)
    public function destroy($id)
    {
        $kos = Kos::find($id);
        if (! $kos) {
            return response()->json(['success' => false, 'message' => 'Kos tidak ditemukan'], 404);
        }

        $kos->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kos berhasil dihapus!',
        ], 200);
    }

    public function currentKos(Request $request)
    {
        $ownerId = $this->resolveOwnerId($request);
        $query = Kos::query();

        if ($ownerId !== null && $ownerId !== '') {
            $query->where('users_id', $ownerId);
        }

        $kos = $query->latest('id')->first();

        if (! $kos) {
            return response()->json([
                'success' => false,
                'message' => 'Kos tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kos,
        ], 200);
    }

    public function allResidents(Request $request)
    {
        $ownerId = $this->resolveOwnerId($request);
        $query = Rooms::with(['kos', 'user']);

        if ($ownerId !== null && $ownerId !== '') {
            $query->where('users_id', $ownerId);
        }

        $residents = $query->get()->map(fn (Rooms $room) => $this->formatResidentRecord($room));

        return response()->json([
            'success' => true,
            'data' => $residents,
        ], 200);
    }

    public function kosResidents(Request $request, $kosId)
    {
        $residents = Rooms::with(['kos', 'user'])
            ->where('kos_id', $kosId)
            ->get()
            ->map(fn (Rooms $room) => $this->formatResidentRecord($room));

        return response()->json([
            'success' => true,
            'data' => $residents,
        ], 200);
    }

    private function formatResidentRecord(Rooms $room): array
    {
        return [
            'periode' => $room->created_at?->format('Y-m') ?? '-',
            'nomor' => $room->nomor_kamar ?? '-',
            'nama' => $room->user?->name ?? '-',
            'tanggalMasuk' => $room->created_at?->format('Y-m-d') ?? '-',
            'tanggalKeluar' => $room->users_id ? '-' : ($room->updated_at?->format('Y-m-d') ?? '-'),
            'status' => $room->users_id ? 'AKTIF' : 'KOSONG',
        ];
    }
}
