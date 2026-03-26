<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EasyKoin;
use App\Models\MyTupai;
use App\Models\TupaiHistori;
use App\Models\UMisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MyTupaiController extends Controller
{
    private const DECAY_PER_MINUTE = 5;

    private const ACTION_THRESHOLD = 70;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $tupai = MyTupai::with('user')->get()->map(function (MyTupai $item) {
            return $this->hitungStatusTerkini($item);
        });

        return response()->json(['success' => true, 'data' => $tupai], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'users_id' => 'required|integer|exists:users,id',
                'nama' => 'required|string|max:255',
            ]);

            $userId = $request->user()?->id ?? $validated['users_id'];

            if (! DB::table('users')->where('id', $userId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            // Cek jika user sudah punya
            $exists = MyTupai::where('users_id', $userId)->first();
            if ($exists) {
                return response()->json(['success' => true, 'data' => $exists]);
            }

            $tupai = MyTupai::create([
                'users_id' => $userId,
                'nama' => $validated['nama'],
                'level' => 1,
                'xp' => 0,
                'level_lapar' => 100,
                'level_stamina' => 100,
                'status' => 'normal',
                'terakhir_makan' => now(),
                'terakhir_tidur' => now(),
                'tidur_sampai' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Berhasil adopsi!', 'data' => $tupai]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $tupai = MyTupai::find($id);
            if (! $tupai) {
                return response()->json(['success' => false, 'message' => 'Tupai tidak ditemukan'], 404);
            }

            $tupai = $this->hitungStatusTerkini($tupai);

            return response()->json(['success' => true, 'data' => $tupai], 200);
        } catch (\Throwable $e) {
            return $this->handleDbError($e);
        }
    }

    private function applyDecay(MyTupai $tupai, string $valueField, string $timeField, Carbon $now): bool
    {
        $lastAction = $tupai->{$timeField} ? Carbon::parse($tupai->{$timeField}) : null;

        if (! $lastAction) {
            return false;
        }

        $minutesPassed = $lastAction->diffInMinutes($now);

        if ($minutesPassed <= 0) {
            return false;
        }

        $decayAmount = $minutesPassed * self::DECAY_PER_MINUTE;
        $tupai->{$valueField} = max(0, (int) $tupai->{$valueField} - $decayAmount);
        $tupai->{$timeField} = $now;

        return true;
    }

    private function applyGain(MyTupai $tupai, string $valueField, string $timeField, Carbon $now, int $target = 100): bool
    {
        $lastAction = $tupai->{$timeField} ? Carbon::parse($tupai->{$timeField}) : null;

        if (! $lastAction) {
            return false;
        }

        $currentValue = (int) $tupai->{$valueField};

        if ($currentValue >= $target) {
            return false;
        }

        $minutesPassed = $lastAction->diffInMinutes($now);

        if ($minutesPassed <= 0) {
            return false;
        }

        $gainAmount = $minutesPassed * self::DECAY_PER_MINUTE;
        $tupai->{$valueField} = min($target, $currentValue + $gainAmount);
        $tupai->{$timeField} = $now;

        return true;
    }

    private function isSleeping(MyTupai $tupai): bool
    {
        if ($tupai->status !== 'sleeping') {
            return false;
        }

        if (! $tupai->tidur_sampai) {
            return true;
        }

        return Carbon::parse($tupai->tidur_sampai)->greaterThan(Carbon::now());
    }

    private function hitungStatusTerkini($tupai)
    {
        $sekarang = Carbon::now();

        $perluDisave = false;

        $sleeping = $this->isSleeping($tupai);
        $sleepUntil = $tupai->tidur_sampai ? Carbon::parse($tupai->tidur_sampai) : null;

        if ($sleeping) {
            $perluDisave = $this->applyDecay($tupai, 'level_lapar', 'terakhir_makan', $sekarang) || $perluDisave;

            $perluDisave = $this->applyGain($tupai, 'level_stamina', 'terakhir_tidur', $sekarang) || $perluDisave;

            if ($tupai->level_stamina >= 100 || ($sleepUntil && $sleepUntil->lessThanOrEqualTo($sekarang))) {
                $tupai->level_stamina = 100;
                $tupai->status = 'normal';
                $perluDisave = true;
            } else {
                $tupai->status = 'sleeping';
            }
        } else {
            if ($tupai->status === 'sleeping' && $sleepUntil && $sleepUntil->lessThanOrEqualTo($sekarang)) {
                $tupai->status = 'normal';
                $tupai->terakhir_tidur = $sleepUntil;
                $tupai->level_stamina = 100;
                $perluDisave = true;
            }

            $perluDisave = $this->applyDecay($tupai, 'level_lapar', 'terakhir_makan', $sekarang) || $perluDisave;
            $perluDisave = $this->applyDecay($tupai, 'level_stamina', 'terakhir_tidur', $sekarang) || $perluDisave;

            if ($tupai->level_lapar < 30) {
                $tupai->status = 'hungry';
            } elseif ($tupai->level_stamina < 30) {
                $tupai->status = 'exhausted';
            } else {
                $tupai->status = 'normal';
            }
        }

        if ($perluDisave) {
            $tupai->save();
        }

        return $tupai;
    }

    public function feed($id)
    {
        try {
            $tupai = MyTupai::find($id);

            if (! $tupai) {
                return response()->json(['success' => false, 'message' => 'Tupai tidak ditemukan'], 404);
            }

            $hargaMakanan = 10;
            $dompet = null;

            if (Schema::hasTable('dompet_koin')) {
                $dompet = EasyKoin::where('users_id', $tupai->users_id)->first();

                if (! $dompet || $dompet->total_koin < $hargaMakanan) {
                    return response()->json(['success' => false, 'message' => 'Koin tidak cukup! Butuh 10 Koin untuk beli makanan.'], 400);
                }
            }

            $tupai = $this->hitungStatusTerkini($tupai);

            if ($tupai->level_lapar >= self::ACTION_THRESHOLD) {
                return response()->json(['success' => false, 'message' => 'Tupai masih kenyang, belum perlu makan.'], 400);
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

            if ($dompet) {
                $dompet->total_koin -= $hargaMakanan;
                $dompet->total_pakai += $hargaMakanan;
                $dompet->save();
            }

            return response()->json(['success' => true, 'message' => 'Tupai berhasil diberi makan!', 'data' => $tupai], 200);
        } catch (\Throwable $e) {
            return $this->handleDbError($e);
        }
    }

    public function sleep($id)
    {
        try {
            $tupai = MyTupai::find($id);
            if (! $tupai) {
                return response()->json(['success' => false, 'message' => 'Tupai tidak ditemukan'], 404);
            }

            $tupai = $this->hitungStatusTerkini($tupai);

            if ($tupai->status === 'sleeping' && $this->isSleeping($tupai)) {
                return response()->json(['success' => false, 'message' => 'Tupai lagi tidur!'], 400);
            }

            if ($tupai->level_stamina >= self::ACTION_THRESHOLD) {
                return response()->json(['success' => false, 'message' => 'Tupai masih segar, belum perlu tidur.'], 400);
            }

            $energiSebelum = $tupai->level_stamina;
            $neededMinutes = (int) ceil((100 - max(0, (int) $tupai->level_stamina)) / self::DECAY_PER_MINUTE);
            $neededMinutes = max(1, $neededMinutes);

            $tupai->status = 'sleeping';
            $tupai->terakhir_tidur = Carbon::now();
            $tupai->tidur_sampai = Carbon::now()->addMinutes($neededMinutes);
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
        } catch (\Throwable $e) {
            return $this->handleDbError($e);
        }
    }

    private function handleDbError(\Throwable $e)
    {
        Log::error('MyTupaiController DB error: '.$e->getMessage(), ['exception' => $e]);

        return response()->json([
            'success' => false,
            'message' => 'Database connection error. Pastikan konfigurasi DB Anda benar dan tidak ada CA file hilang.',
            'error' => $e->getMessage(),
        ], 503);
    }

    private function updateMissionProgress($userId, $jenisMisi)
    {
        if (! Schema::hasTable('misi_user') || ! Schema::hasTable('misi')) {
            return;
        }

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
        try {
            if (! DB::table('users')->where('id', $userId)->exists()) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }

            $tupai = MyTupai::where('users_id', $userId)->first();
            if (! $tupai) {
                return response()->json(['success' => false, 'message' => 'Belum ada tupai'], 404);
            }
            // Hitung status terbaru sebelum dikirim ke frontend
            $tupai = $this->hitungStatusTerkini($tupai);

            return response()->json(['success' => true, 'data' => $tupai]);
        } catch (\Throwable $e) {
            return $this->handleDbError($e);
        }
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
