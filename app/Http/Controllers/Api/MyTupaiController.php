<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MyTupai;
use App\Models\TupaiHistori;
use App\Models\UMisi;
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
        $validated = $request->validate([
        'users_id' => 'required',
        'nama' => 'required|string|max:255',
    ]);

    // Cek jika user sudah punya
    $exists = MyTupai::where('users_id', $validated['users_id'])->first();
    if ($exists) return response()->json(['success' => true, 'data' => $exists]);

    $tupai = MyTupai::create([
        'users_id' => $validated['users_id'],
        'nama' => $validated['nama'],
        'level' => 1,
        'xp' => 0,
        'level_lapar' => 100,
        'level_stamina' => 100,
        'status' => 'normal',
        'terakhir_makan' => now(),
        'terakhir_tidur' => now(),
    ]);

    return response()->json(['success' => true, 'message' => 'Berhasil adopsi!', 'data' => $tupai]);
    

    $tupai = MyTupai::create([
        'users_id' => $validated['users_id'],
        'nama' => $validated['nama'],
        'level' => 1,
        'xp' => 0,
        'level_lapar' => 100,
        'level_stamina' => 100,
        'status' => 'normal',
        'terakhir_makan' => now(),
        'terakhir_tidur' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Tupai berhasil diadopsi!',
        'data' => $tupai
    ]);
    }

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $tupai = MyTupai::find($id);
        if (! $tupai) {
            return response()->json(['success' => false, 'message' => 'Tupai tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $tupai], 200);
    }

    private function hitungStatusTerkini($tupai)
    {
        $sekarang = Carbon::now();

        $jamSejakMakan = $sekarang->diffInHours(Carbon::parse($tupai->terakhir_makan));
        $jamSejakTidur = $sekarang->diffInHours(Carbon::parse($tupai->terakhir_tidur));

        $tupai->level_lapar -= ($jamSejakMakan * 2);
        $tupai->level_stamina -= ($jamSejakTidur * 3);

        if ($tupai->level_lapar < 0) {
            $tupai->level_lapar = 0;
        }
        if ($tupai->level_stamina < 0) {
            $tupai->level_stamina = 0;
        }

        if ($tupai->level_lapar < 30) {
            $tupai->status = 'hungry';
        } elseif ($tupai->level_stamina < 30) {
            $tupai->status = 'exhausted';
        } else {
            $tupai->status = 'normal';
        }

        $tupai->save();

        return $tupai;
    }

    public function feed($id)
    {
        $tupai = MyTupai::find($id);
        if (! $tupai) {
            return response()->json(['success' => false, 'message' => 'Tupai tidak ditemukan'], 404);
        }

        $tupai = $this->hitungStatusTerkini($tupai);

        if ($tupai->level_lapar >= 100) {
            return response()->json(['success' => false, 'message' => 'Tupai masih sangat kenyang!'], 400);
        }

        $laparSebelum = $tupai->level_lapar;
        $tupai->level_lapar += 30;
        $tupai->xp += 10;
        if ($tupai->level_lapar > 100) {
            $tupai->level_lapar = 100;
        }

        if ($tupai->xp >= 100) {
            $tupai->level += 1;
            $tupai->xp = 0;

            CoinController::updateCoin(
                $tupai->users_id,
                $tupai->level * 20,
                'level_up',
                'Bonus naik level Tupai ke level '.$tupai->level
            );
        }

        $tupai->terakhir_makan = Carbon::now();
        $tupai->status = 'happy';
        $tupai->save();

        $this->updateMissionProgress($tupai->users_id, 'feed');

        TupaiHistori::create([
            'myTupai_id' => $tupai->id,
            'aktivitas_tipe' => 'feed',
            'aktivitas_waktu' => Carbon::now(),
            'lapar_sebelum' => $laparSebelum,
            'lapar_setelah' => $tupai->level_lapar,
            'energi_sebelum' => $tupai->level_stamina,
            'energi_setelah' => $tupai->level_stamina,
            'notes' => 'Tupai diberi makan, nyam nyam!',
        ]);

        return response()->json(['success' => true, 'message' => 'Tupai berhasil diberi makan!', 'data' => $tupai], 200);
    }

    public function sleep($id)
    {
        $tupai = MyTupai::find($id);
        if (! $tupai) {
            return response()->json(['success' => false, 'message' => 'Tupai tidak ditemukan'], 404);
        }

        $tupai = $this->hitungStatusTerkini($tupai);

        if ($tupai->status === 'sleeping') {
            return response()->json(['success' => false, 'message' => 'Tupai lagi tidur!'], 400);
        }

        $energiSebelum = $tupai->level_stamina;
        $tupai->level_stamina = 100;
        $tupai->status = 'sleeping';
        $tupai->terakhir_tidur = Carbon::now();
        $tupai->tidur_sampai = Carbon::now()->addHours(8);
        $tupai->save();

        $this->updateMissionProgress($tupai->users_id, 'sleep');

        TupaiHistori::create([
            'myTupai_id' => $tupai->id,
            'aktivitas_tipe' => 'sleep',
            'aktivitas_waktu' => Carbon::now(),
            'lapar_sebelum' => $tupai->level_lapar,
            'lapar_setelah' => $tupai->level_lapar,
            'energi_sebelum' => $energiSebelum,
            'energi_setelah' => $tupai->level_stamina,
            'notes' => 'Tupai pergi tidur zzz...',
        ]);

        return response()->json(['success' => true, 'message' => 'Tupai lagi menikmati kasur!', 'data' => $tupai], 200);
    }

    private function updateMissionProgress($userId, $jenisMisi)
    {
        $misiUser = UMisi::where('users_id', $userId)
            ->where('status', 'in_progress')
            ->whereHas('mission', function ($query) use ($jenisMisi) {
                $query->where('jenis', $jenisMisi);
            })
            ->first();

        if ($misiUser) {
            $misiUser->progress_level += 1;

            if ($misiUser->progress_level >= 3) {
                $misiUser->status = 'completed';
                $misiUser->completed_at = Carbon::now();
            }
            $misiUser->save();
        }
    }

    public function checkMyTupai($userId)
{
    $tupai = MyTupai::where('users_id', $userId)->first();
    if (!$tupai) {
        return response()->json(['success' => false, 'message' => 'Belum ada tupai'], 404);
    }
    // Hitung status terbaru sebelum dikirim ke frontend
    $tupai = $this->hitungStatusTerkini($tupai);
    return response()->json(['success' => true, 'data' => $tupai]);
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
