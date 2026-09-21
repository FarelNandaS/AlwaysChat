<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('online', function ($user) {
    if ($user) {
        return [
            'id'=>$user->id,
            'name'=>$user->name,
        ];
    }
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    return Conversation::where('id', $conversationId)->whereHas('users', function ($query) use ($user) {
        $query->where('users.id', $user->id);
    })->exists();
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});