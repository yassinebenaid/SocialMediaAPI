<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentRepository extends Repository
{
    public function create(Post $post, Request $request)
    {
        try {
            $comment = $post->comments()->create([
                "body" => $request->body,
                "user_id" => auth()->user()->id
            ]);
        } catch (\Throwable $th) {
            throw new GeneralException("Something went wrong, couldn't add comment ! ", 422);
        }

        return $comment;
    }

    public function update(Comment $comment, Request $request)
    {
        try {
            $comment->update([
                "body" => $request->body ?? $comment->body
            ]);
        } catch (\Throwable $th) {
            throw new GeneralException("Somthing went wrong, couldn't update comment ! ", 422);
        }

        return $comment;
    }
}
