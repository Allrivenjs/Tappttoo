<?php

namespace App\Interfaces\Chat;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface RoomInterface
{
    public function createRoom(bool $type): Builder|\Illuminate\Database\Eloquent\Model;

    public function getUsers($roomId): User | array;

    public function addUser($roomId, $userId): void;

    public function matchUser($receiverId, $userId);

//    public function removeUser($roomId, $userId);
}
