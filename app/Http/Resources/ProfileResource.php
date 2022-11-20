<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    protected static $friend_requests = null;
    protected static $MyFriends = null;

    public function __construct($resource)
    {
        parent::__construct($resource);

        if (is_null(self::$friend_requests)) {
            self::$friend_requests = auth()->user()->friendRequests;
        }

        if (is_null(self::$MyFriends)) {
            self::$MyFriends = auth()->user()->friends;
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

        return [

            "id" => $this->id,
            "attributes" => [
                "username" => $this->username,
                "email" => $this->email,
                "gender" => $this->gender,
                "birthday" => $this->birthday,
                "bio" => $this->bio,
                "region" => $this->region,
                "phone_number" => $this->phone_number,
                "profile_image" => env("APP_URL") . $this->profile_image
            ],
            "hasSentMeFriendRequest" => $hasSentMeRequest,
            "isMyFriend" => (bool) $isMyFriend,
            "relationships" => [
                "posts" => $this->posts->load(["comments", "comments.user"]),
                "groups" => $this->groups
            ]
        ];
    }
}
