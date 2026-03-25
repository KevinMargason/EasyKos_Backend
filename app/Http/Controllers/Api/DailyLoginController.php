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
                $koinReward = 10;

                $streakLog = StreakLog::where('user_id', $userId)->first();
                $dompet = EasyKoin::firstOrCreate(
                    ['user_id' => $userId],
                    ['total_koin' => 0]
                );

                if ($streakLog) {
                    $lastLoginDate = Carbon::parse($streakLog->last_login_date)->startOfDay();

                    if ($lastLoginDate->eq($today)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Kamu sudah klaim koin hari ini. Besok balik lagi ya, bosku!',
                        ], 400);
                    }

                    if ($lastLoginDate->eq($yesterday)) {
                        $streakLog->streak_count += 1;

                        if ($streakLog->streak_count % 7 == 0) {
                            $koinReward += 50;
                        }
                    } else {
                        $streakLog->streak_count = 1;
                    }

                    $streakLog->last_login_date = now();
                    $streakLog->save();
                } else {
                    $streakLog = StreakLog::create([
                        'user_id' => $userId,
                        'streak_count' => 1,
                        'last_login_date' => now()
                    ]);
                }

                $dompet->total_koin += $koinReward;
                $dompet->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Klaim harian berhasil!',
                    'data' => [
                        'streak_hari_ini' => $streakLog->streak_count,
                        'koin_didapat' => $koinReward,
                        'total_koin_sekarang' => $dompet->total_koin
                    ]
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengklaim koin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStreakStatus(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User tidak valid.'], 401);
        }

        $streakLog = StreakLog::where('user_id', $userId)->first();
        $dompet = EasyKoin::where('user_id', $userId)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'streak_count' => $streakLog ? $streakLog->streak_count : 0,
                'last_login_date' => $streakLog ? $streakLog->last_login_date->toDateString() : null,
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
