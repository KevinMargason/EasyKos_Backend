<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EasyKoin;
use App\Models\RewardRedemption;
use App\Models\UsersVoucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RewardController extends Controller
{

    public function redeem(Request $request)
    {
        $request->validate([
            'users_id'   => 'required|integer',
            'voucher_id' => 'required|integer',
            'harga_koin' => 'required|integer'
        ]);

        return DB::transaction(function () use ($request) {
            $wallet = EasyKoin::where('users_id', $request->users_id)->first();
            if (!$wallet || $wallet->total_koin < $request->harga_koin) {
                return response()->json(['success' => false, 'message' => 'Koin kamu tidak cukup!'], 400);
            }

            $potongKoin = CoinController::updateCoin(
                $request->users_id,
                $request->harga_koin,
                'redeem_reward',
                'Penukaran koin dengan Voucher ID: ' . $request->voucher_id,
                'debit'
            );

            $redemption = RewardRedemption::create([
                'users_id'          => $request->users_id,
                'voucher_id'        => $request->voucher_id,
                'tanggal_tukar'     => Carbon::now(),
                'jumlah_koin_tukar' => $request->harga_koin,
                'status'            => 'success'
            ]);

            UsersVoucher::create([
                'users_id'   => $request->users_id,
                'voucher_id' => $request->voucher_id,
                'is_used'    => false,
                'claimed_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penukaran berhasil! Voucher sudah masuk ke akunmu.',
                'data'    => $redemption
            ], 201);
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
