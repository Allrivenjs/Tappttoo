<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageNotification;
use App\Http\Resources\RoomsCollection;
use App\Http\Resources\RoomsResource;
use App\Interfaces\Chat\ChatInterface;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class Chat implements ChatInterface
{
    public function createRoom(bool $type = false): Builder|\Illuminate\Database\Eloquent\Model
    {
        return Room::query()->create([
            'name' => Str::uuid(),
            'type' => $type,
        ]);
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

    public function sendMessage($roomId, $message): void
    {
        $user = Auth::guard('api')->user()->getAuthIdentifier();
        $data = [
            'message' => $message,
            'user_id' => $user,
            'room_id' => $roomId,
        ];
        $message = new Message($data);
        $message->save();
        $user2 = Room::query()->find($roomId)->users()->where('user_id', '!=', $user)->first();
        $user2 = User::find($user2->id);
        $user2->notify(new \App\Notifications\MessageNotification($message, $user, $roomId));
        broadcast(new MessageNotification($data))->toOthers();
        // enviar notificacion al usuario resecptor
//        broadcast(new \App\Notifications\MessageNotification($message, $user2, $roomId))->toOthers();


    }

    public function getRooms(): array
    {
        return array_values(collect(User::query()->with([
            'rooms'=> [
                'users' => fn ($q) => $q->where('users.id', '!=', Auth::guard('api')->user()->getAuthIdentifier()),
                'lastMessage'
            ]
        ])->find(Auth::guard('api')->user()->getAuthIdentifier())->rooms)->sortByDesc(function ($room) {
            return Carbon::parse($room->lastMessage->first()->created_at ?? Carbon::now())->format('Y-m-d H:i:s');
        })->map(function ($room) {
            $lastMessage = $room->lastMessage->first();
            $users = [
                'users' => [
                    ...$room->users->map(fn ($user) => [
                        ...$user->toArray(),
                        'subscription_active' => $user->getSubscriptionActive()
                    ])
                ]
            ];
            if ($lastMessage === null) {
                return [
                    ...$room->toArray(),
                    'last_message' => [],
                    'quotation' => $room->lastQuotation->first(),
                    ...$users
                ];
            }

            return [
                ...$room->toArray(),
                'last_message' => [
                    ...$lastMessage->toArray(),
                    'created_at' => Carbon::parse($lastMessage->created_at)->diffForHumans(['parts' => 1, 'join'=>true]),
                ],
                'quotation' => $room->lastQuotation->first(),
                ...$users
            ];
        })->toArray());
    }

    public function getMessages($roomId)
    {
        return Room::query()->with([
            'messages' => fn ($q) => $q->with('user')->orderByDesc('created_at'),
            'users' => fn ($q) => $q->where('users.id', '!=', Auth::guard('api')->user()->getAuthIdentifier()),
        ])->find($roomId)?->messages;
    }

    public function matchUser($receiverId, $userId): \Illuminate\Database\Eloquent\Model|Builder|bool|null
    {
        $room = Room::query()
            ->whereHas('users', function ($query) use ($userId, $receiverId) {
                $query->where('user_id', $userId)
                    ->orWhere('user_id', $receiverId);
            }, '=', 2); // El número 2 indica que deben existir exactamente 2 usuarios en la sala
        return $room->exists() ? $room->first() : false;
    }
}
