<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageNotification;
use App\Http\Resources\RoomsCollection;
use App\Http\Resources\RoomsResource;
use App\Interfaces\Chat\ChatInterface;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class Chat implements ChatInterface
{
    public function createRoom(bool $type): Room
    {
        $name = Str::uuid();
        $room = new Room();
        $room->name = $name;
        $room->type = $type;
        $room->save();

        return $room;
    }

    public function getUsers($roomId): User | array
    {
        $room = Room::query()->find($roomId);

        return $room->users;
    }

    public function addUser($roomId, $userId): void
    {
        $room = Room::query()->find($roomId);
        $user = User::query()->find($userId);
        $room->users()->attach($user);
    }

    public function sendMessage($roomIdId, $message): void
    {
        $user = Auth::guard('api')->user()->getAuthIdentifier();
        $data = [
            'message' => $message,
            'user_id' => $user,
            'room_id' => $roomIdId,
        ];
        $message = new Message($data);
        $message->save();
        $user2 = Room::query()->find($roomIdId)->users()->where('user_id', '!=', $user)->first();
        broadcast(new MessageNotification($data))->toOthers();
        // enviar notificacion al usuario resecptor
        Notification::send($user2, new \App\Notifications\MessageNotification($message, $user, $roomIdId));


    }

    public function getRooms()
    {
        return User::query()->with([
            'rooms'=> [
                'users' => fn ($q) => $q->where('users.id', '!=', Auth::guard('api')->user()->getAuthIdentifier()),
            ]
        ])->find(Auth::guard('api')->user()->getAuthIdentifier())->rooms->map(function ($room) {
            return [
                ...$room->toArray(),
                'last_message' => $room->lastMessage->first(),
                'quotation' => $room->lastQuotation->first(),
            ];
        });
    }

    public function getMessages($roomId)
    {
        $room = Room::query()->with('messages.user')->find($roomId);
        return $room->messages;
    }

    public function matchUser($receiverId, $userId): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model| bool
    {
        $personOne = Room::query()->whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();
        $personTwo = Room::query()->whereHas('users', function ($query) use ($receiverId) {
            $query->where('user_id', $receiverId);
        })->first();

        if ($personOne && $personTwo) {
            return $personOne->id === $personTwo->id ? $personOne : false;
        }

        return false;
    }
}
