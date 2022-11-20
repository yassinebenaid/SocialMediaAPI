<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "attributes" => [
                "body" => $this->body,
                "created_at" => [
                    "date" => $this->created_at->format("Y-m-d H:i"),
                    "readable" => $this->created_at->diffForHumans()
                ]
            ],
            "relationships" => [
                "user" => new UserRsource($this->user),
                "post" => $this->post_id
            ],
            "links" => [
                "post" => route("posts.show", ["post" => $this->post_id])
            ]
        ];
    }
}
