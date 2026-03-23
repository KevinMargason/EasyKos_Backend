<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $userId = $request->user()->id;

        $threads = DB::table('chat_threads')
            ->where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $threads
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
    public function show(string $id, $threadId)
    {
        //
        $thread = DB::table('chat_threads')->where('id', $threadId)->first();

        if (!$thread) {
            return response()->json(['success' => false, 'message' => 'Ruang chat tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $thread
        ], 200);
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

    public function messages($threadId)
    {
        $messages = DB::table('chat_messages')
            ->where('thread_id', $threadId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $messages
        ], 200);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'message'     => 'required|string'
        ]);

        $senderId = $request->user()->id;
        $receiverId = $request->receiver_id;

        $thread = DB::table('chat_threads')
            ->where(function ($query) use ($senderId, $receiverId) {
                $query->where('user1_id', $senderId)->where('user2_id', $receiverId);
            })
            ->orWhere(function ($query) use ($senderId, $receiverId) {
                $query->where('user1_id', $receiverId)->where('user2_id', $senderId);
            })
            ->first();

        if (!$thread) {
            $threadId = DB::table('chat_threads')->insertGetId([
                'user1_id'   => $senderId,
                'user2_id'   => $receiverId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $threadId = $thread->id;
            DB::table('chat_threads')->where('id', $threadId)->update(['updated_at' => now()]);
        }

        $messageId = DB::table('chat_messages')->insertGetId([
            'thread_id'  => $threadId,
            'sender_id'  => $senderId,
            'message'    => $request->message,
            'is_read'    => 0,
            'created_at' => now()
        ]);

        $newMessage = DB::table('chat_messages')->where('id', $messageId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil terkirim!',
            'data'    => $newMessage
        ], 201);
    }
}
