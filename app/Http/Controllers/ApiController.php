<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function checkUser(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255']
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

        $conversation = Conversation::whereHas('users', function ($q) use ($currentUser) {
            $q->where('users.id', $currentUser->id);
        })->whereHas('users', function ($q) use ($targetUser) {
            $q->where('users.id', $targetUser->id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create();
            $conversation->users()->attach([$currentUser->id, $targetUser->id]);
        }

        return back()->with('flash', [
            'conversation' => [
                'id' => $conversation->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'lastMsg' => 'Chat baru dimulai',
                'time' => 'Now',
                'online' => false,
            ]
        ]);
    }
}
