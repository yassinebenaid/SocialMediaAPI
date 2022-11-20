<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostRepository extends Repository
{
    public function create($request)
    {
        try {

            if ($request->group && $request->group instanceof Group) return  $this->postInGroup($request);

            $post = Post::create([
                "user_id" => auth()->user()->id,
                "type" => $request->type,
                "title" => $request->title ?? null,
                "body" => $request->body,
                "image" => $this->saveImage($request->image, "posts"),
                "event" =>  "normal"
            ]);
            // 
        } catch (\Throwable $th) {

            if ($th instanceof GeneralException)  throw new GeneralException($th->getMessage(), $th->getCode());

            throw new GeneralException("couldn't create post , please try again !", 422);
        }

        return $post;
    }

    public function postInGroup(Request $request)
    {
        return DB::transaction(function () use ($request) {

            $post = Post::create([
                "user_id" => auth()->user()->id,
                "type" => $request->type,
                "title" => $request->title ?? null,
                "body" => $request->body,
                "image" => $this->saveImage($request->image, "posts"),
                "event" => "in_group"
            ]);

            $group = $request->group;

            if (!$group->isMember(auth()->user()->id)) throw new GeneralException("Unauthenticated", 403);

            $group->posts()->attach($post);

            return $post;
        });
    }

    public function update(Post $post, $request)
    {
        try {

            $post->update([
                "title" => $request->title ?? $post->title,
                "body" =>  $request->body ?? $post->body,
            ]);
            //
        } catch (\Throwable $th) {
            throw new GeneralException("couldn't update the post , please try again !", 422);
        }
        return $post;
    }

    public function delete(Post $post)
    {
        return $post->delete();
    }
}
