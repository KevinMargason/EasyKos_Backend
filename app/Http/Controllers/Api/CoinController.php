<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EasyKoin;
use App\Models\EasyKoinKoin;
use App\Models\TransaksiKoin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function getBalance($userId)
    {
        $wallet = EasyKoin::firstOrCreate(
            ['users_id' => $userId],
            ['total_koin' => 0, 'total_dapat' => 0, 'total_pakai' => 0]
        );
        return response()->json(['success' => true, 'data' => $wallet], 200);
    }

    public static function updateCoin($userId, $jumlah, $asal, $deskripsi, $direction = 'credit', $refId = 0)
    {
        return DB::transaction(function () use ($userId, $jumlah, $asal, $deskripsi, $direction, $refId) {
            $wallet = EasyKoin::firstOrCreate(['users_id' => $userId]);

            $koinSebelum = $wallet->total_koin;

            if ($direction == 'credit') {
                $wallet->total_koin += $jumlah;
                $wallet->total_dapat += $jumlah;
            } else {
                if ($wallet->total_koin < $jumlah) return false;
                $wallet->total_koin -= $jumlah;
                $wallet->total_pakai += $jumlah;
            }
            $wallet->save();

            TransaksiKoin::create([
                'users_id' => $userId,
                'dompet_koin_id' => $wallet->id,
                'asal_koin' => $asal,
                'reference_id' => $refId,
                'direction' => $direction,
                'jumlah' => $jumlah,
                'koin_sebelum' => $koinSebelum,
                'koin_setelah' => $wallet->total_koin,
                'deskripsi' => $deskripsi
            ]);

            return true;
        });
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
