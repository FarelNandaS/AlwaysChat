<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    public function userKeys(Request $request)
    {
        return response()->json([
            'encrypted_private_key' => $request->user()->encrypted_private_key,
            'salt' => $request->user()->salt,
            'iv' => $request->user()->iv,
        ]);
    }

    public function getPublicKey(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->public_key) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan atau belum memiliki kunci publik.'
            ], 403);
        }

        return response()->json([
            'public_key' => $user->public_key
        ]);
    }

    public function addConversation(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'ciphertext' => ['required', 'string'],
            'iv' => ['required', 'string'],
        ]);

        $currentUser = $request->user();

        if (strtolower($request->email) === strtolower($currentUser->email)) {
            throw ValidationException::withMessages([
                'email' => 'Kamu tidak bisa memulai chat dengan email milikmu sendiri.'
            ]);
        }

        $targetUser = User::where('email', $request->email)->first();

        if (!$targetUser) {
            throw ValidationException::withMessages([
                'email' => 'Pengguna dengan email tersebut tidak ditemukan.',
            ]);
        }

        $conversation = DB::transaction(function () use ($currentUser, $targetUser, $request) {
            $existingConversation = Conversation::whereHas('users', function ($q) use ($currentUser) {
                $q->where('users.id', $currentUser->id);
            })->whereHas('users', function ($q) use ($targetUser) {
                $q->where('users.id', $targetUser->id);
            })->first();

            if ($existingConversation) {
                $conversation = $existingConversation;
            } else {
                $conversation = Conversation::create();
                $conversation->users()->attach([$currentUser->id, $targetUser->id]);
            }

            $conversation->message()->create([
                'sender_id' => $currentUser->id,
                'ciphertext' => $request->ciphertext,
                'iv' => $request->iv ?? '',
            ]);

            $conversation->touch();

            return $conversation;
        });

        return back()->with('flash', [
            'conversation' => [
                'id' => $conversation->id,
                'recipient_id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'public_key' => $targetUser->public_key,
                'iv' => $request->iv,
                'lastMsg' => $request->ciphertext,
                'time' => 'Now',
                'online' => false,
            ]
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required||integer',
            'ciphertext' => 'required||string',
            'iv' => 'required||string',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        $message = $conversation->message()->create([
            'sender_id' => $request->user()->id,
            'ciphertext' => $request->ciphertext,
            'iv' => $request->iv
        ]);

        $conversation->touch();

        $receiver = $conversation->users->where('id', '!=', Auth::user()->id)->first();

        broadcast(new MessageSent($message, $receiver->id))->toOthers();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender_id' => $request->user()->id,
                'ciphertext' => $request->ciphertext,
                'iv' => $request->iv,
                'created_at' => $message->created_at->format('H:i'),
            ]
        ]);
    }

    public function getMessages(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->users()->where('users.id', $request->user()->id)->exists(), 403);

        $messages = $conversation->message()->with('sender:id,name')->orderBy('created_at', 'asc')->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender?->name,
                'ciphertext' => $msg->ciphertext,
                'iv' => $msg->iv,
                'created_at' => $msg->created_at->format('H:i')
            ];
        });

        return response()->json($messages);
    }
}
