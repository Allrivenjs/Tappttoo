<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Interfaces\Chat\RoomInterface;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ChatController extends Controller
{
    public function __construct(private RoomInterface $room)
    {
    }

    public function getRooms(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return Response($this->room->getRooms());
    }

    public function getMessages($roomId): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return Response($this->room->getMessages($roomId));
    }

    /**
     * @throws \Throwable
     */
    public function getExistRoom(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
        ]);
        $userId = Auth::guard('api')?->user()?->getAuthIdentifier();
        $receiver_id = $request->query('receiver_id');
        throw_if(is_null($receiver_id), 'Receiver id is required for query param');
        throw_if((int) $receiver_id == (int) $userId, 'You can not chat with yourself');
        $match = $this->room->matchUser((int) $receiver_id, (int) $userId);
        dd($match);
        $response = $match ?: $this->createChatRoom($receiver_id)->first();
        return response($response);
    }

    public function createChatRoom($receiver_id): \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
    {
        $room = $this->room->createRoom();
        $userId = Auth::guard('api')?->user()?->getAuthIdentifier();
        $this->room->addUser($room->id, $receiver_id);
        $this->room->addUser($room->id, $userId);
        return $room;
    }

    public function sendMessage(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $room = $request->validate([
            'room_id' => 'required|integer',
            'message' => 'required|string',
        ]);
        $this->room->sendMessage($room['room_id'], $room['message']);

        return Response(null);
    }

    public function markAsRead(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $request->validate([
            'message' => 'required|exists:messages,id',
        ]);
        $message = Message::query()->find($request->query('message'))->markAsReadTo();
//        Notification::send(
//            $message->room()->users()->where('id', '!=', $message->user_id)->get(),
//        );
        return Response(null);
    }
}
