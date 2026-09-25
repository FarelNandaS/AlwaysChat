<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $conversations = $user->conversations()->with([
            'users' => function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id);
            },
            'latestMessage'
        ])->latest('updated_at')->get()->map(function ($conv) use ($user) {
            $recipient = $conv->users->first();

            $hasUnread = $conv->message()->where('sender_id', '!=', $user->id)->whereNull('read_at')->exists();

            return [
                'id' => $conv->id,
                'recipient_id' => $recipient->id,
                'name' => $recipient?->name ?? 'Penguna',
                'email' => $recipient?->email ?? '',
                'public_key' => $recipient?->public_key,
                'iv' => $conv->latestMessage?->iv,
                'lastMsg' => $conv->latestMessage?->ciphertext ?? 'Belum ada pesan',
                'time' => $conv->updated_at->format('H:i'),
                'online' => false,
                'has_unread' => $hasUnread,
            ];
        });

        return Inertia::render('Dashboard', [
            'conversations' => $conversations
        ]);
    }

    public function settings() {
        return Inertia::render('Settings');
    }
}
