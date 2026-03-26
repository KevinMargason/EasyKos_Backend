<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyLoginLog;
use App\Models\EasyKoin;
use App\Models\StreakLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyLoginController extends Controller
{

    public function claimLogin(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId && $request->user()) {
            $userId = $request->user()->id;
        }

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User tidak valid.'], 401);
        }

        try {
            return DB::transaction(function () use ($userId) {

                $today = Carbon::today();
                $yesterday = Carbon::yesterday();
                $koinReward = 50; // Langsung kita kasih 50 koin aja bos!

                $streakLog = StreakLog::where('users_id', $userId)->first();
                $dompet = EasyKoin::firstOrCreate(
                    ['users_id' => $userId],
                    ['total_koin' => 0, 'total_dapat' => 0, 'total_pakai' => 0]
                );

                if ($streakLog) {
                    $lastUpdate = Carbon::parse($streakLog->updated_at)->startOfDay();

                    // Skenario A: Udah klaim hari ini
                    if ($lastUpdate->eq($today) && $streakLog->streak_sekarang > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Sudah klaim hari ini',
                        ], 400);
                    }

                    // Skenario B: Streak berlanjut
                    if ($lastUpdate->eq($yesterday)) {
                        $streakLog->streak_sekarang += 1;
                        if ($streakLog->streak_sekarang > $streakLog->top_streak) {
                            $streakLog->top_streak = $streakLog->streak_sekarang;
                        }
                    }
                    // Skenario C: Streak putus
                    else {
                        $streakLog->streak_sekarang = 1;
                    }

                    $streakLog->updated_at = now(); // Kita pake updated_at sebagai penanda hari
                    $streakLog->save();
                } else {
                    // Skenario D: User baru pertama kali klaim
                    $streakLog = StreakLog::create([
                        'users_id' => $userId,
                        'jenis' => 'login',
                        'streak_sekarang' => 1,
                        'top_streak' => 1
                    ]);
                }

                // Tambah Koin
                $dompet->total_koin += $koinReward;
                $dompet->total_dapat += $koinReward;
                $dompet->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Klaim berhasil!',
                    'data' => [
                        'streak_hari_ini' => $streakLog->streak_sekarang,
                        'koin_didapat' => $koinReward,
                        'total_koin_sekarang' => $dompet->total_koin
                    ]
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStreakStatus(Request $request)
    {
        $userId = $request->input('user_id');
        if (!$userId && $request->user()) {
            $userId = $request->user()->id;
        }

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User tidak valid.'], 401);
        }

        $streakLog = StreakLog::where('users_id', $userId)->first();
        $dompet = EasyKoin::where('users_id', $userId)->first();

        // Kita akali: karena nggak ada 'last_login_date', kita cek dari 'updated_at'
        $lastLoginDate = $streakLog ? Carbon::parse($streakLog->updated_at)->toDateString() : null;

        return response()->json([
            'success' => true,
            'data' => [
                'streak_count' => $streakLog ? $streakLog->streak_sekarang : 0,
                'last_login_date' => $lastLoginDate,
                'total_koin' => $dompet ? $dompet->total_koin : 0
            ]
        ], 200);
    }
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
    public function show(string $id)
    {
        //
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
