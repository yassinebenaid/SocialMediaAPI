<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $post = [
            "id" => $this->id,
            "attributes" => [
                "title" => $this->title,
                "body" => $this->body,
                "type" => $this->type,
                "event" => $this->event,
                "image" => $this->image ? (env("APP_URL") . $this->image) : null,
                "created_at" => [
                    "data" => $this->created_at->format("Y-m-d H:i"),
                    "readable" => $this->created_at->diffForHumans()
                ]
            ],
            "relationships" => [
                "user" => new UserRsource($this->user),
                "likes_count" => $count = $this->likes->count(),
                "isLikedByTheUser" => (bool) $this->likes()->find(auth()->user()->id),
                "lastComment" => $this->comments()->orderBy("id", "desc")->first()
            ],
        ];

        if (Post::$scope === "min") return $post;

        return [
            "id" => $this->id,
            "attributes" => [
                "title" => $this->title,
                "body" => $this->body,
                "type" => $this->type,
                "event" => $this->event,
                "image" => $this->image ? (env("APP_URL") . $this->image) : null,
                "created_at" => [
                    "data" => $this->created_at->format("Y-m-d H:i"),
                    "readable" => $this->created_at->diffForHumans()
                ]
            ],
            "relationships" => [
                "user" => new UserRsource($this->user),
                "likes_count" => $count = $this->likes->count(),
                "likes" => $this->when($count > 0, UserRsource::collection($this->likes)),
                "comments" => $this->comments

            ],
            "links" => [
                "post" => route("posts.show", ["post" => $this->id])
            ]
        ];
    }
}
