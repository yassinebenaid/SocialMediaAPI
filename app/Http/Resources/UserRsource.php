<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRsource extends JsonResource
{
    protected static $friend_requests = null;
    protected static $MyFriends = null;

    public function __construct($resource)
    {
        parent::__construct($resource);

        if (is_null(self::$friend_requests)) {
            self::$friend_requests = auth()->user()?->friendRequests;
        }

        if (is_null(self::$MyFriends)) {
            self::$MyFriends = auth()->user()?->friends;
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $hasSentMeRequest = self::$friend_requests?->contains(function ($value, $key) {
            return (int)$value->id === (int)$this->id;
        });

        $isMyFriend = self::$MyFriends?->contains(function ($value, $key) {
            return (int)$value->id === (int)$this->id;
        });


        $user = [
            "id" => $this->id,
            "username" => $this->username,
            "profile_image" => env("APP_URL") . $this->profile_image,
            "hasSentMeFriendRequest" => $hasSentMeRequest,
            "isMyFriend" => (bool) $isMyFriend,
            "role" => $this->when($this->pivot?->role ?? false, $this->pivot?->role),
            "token" => $this->when($this->accessToken, $this->accessToken),
            "isMe" => ($this->id === auth()->user()?->id) ?? false
        ];

        return $user;
    }
}
