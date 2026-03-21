<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Misi;
use App\Models\UMisi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $userId = $request->query('users_id');

        $missions = Misi::where('is_active', 1)->get();

        foreach ($missions as $misi) {
            $userProgress = UMisi::where('misi_id', $misi->id)
                ->where('users_id', $userId)
                ->whereDate('tanggal', Carbon::today())
                ->first();
            $misi->user_status = $userProgress;
        }

        return response()->json(['success' => true, 'data' => $missions], 200);
    }

    public function claimReward(Request $request)
    {
        $request->validate([
            'misi_user_id' => 'required|string'
        ]);

        $misiUser = UMisi::with('mission')->where('id', $request->misi_user_id)->first();

        if (!$misiUser) {
            return response()->json(['success' => false, 'message' => 'Data misi tidak ditemukan'], 404);
        }

        if ($misiUser->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Misi belum selesai atau sudah diklaim!'], 400);
        }

        $misiUser->update([
            'status' => 'claimed',
            'claimed_at' => Carbon::now()
        ]);

        CoinController::updateCoin(
            $misiUser->users_id,
            $misiUser->mission->coin,
            'mission_claim',
            'Hadiah menyelesaikan misi: ' . $misiUser->mission->nama,
            'credit',
            $misiUser->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Hadiah berhasil diklaim!',
            'reward' => [
                'coin' => $misiUser->mission->coin,
                'xp' => $misiUser->mission->xp
            ]
        ], 200);
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
