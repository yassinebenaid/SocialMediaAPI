<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralException;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Repositories\CommentRepository;
use Illuminate\Validation\UnauthorizedException;

class CommentController extends Controller
{
    public function __construct(
        protected CommentRepository $commentRepo
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $comments = Comment::with("user")->paginate();
        return CommentResource::collection($comments);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCommentRequest $request, Post $post)
    {
        $created = $this->commentRepo->create($post, $request);

        return response()->json([
            "success" => true,
            "message" => "comment created successfully .",
            "comment" => new CommentResource($created)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show($post, Comment $comment)
    {
        return response()->json([
            "success" => true,
            "comment" => new CommentResource($comment)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCommentRequest $request, $post, Comment $comment)
    {
        $updated = $this->commentRepo->update($comment, $request);

        return response()->json([
            "success" => true,
            "message" => "comment updated successfully .",
            "comment" => new CommentResource($updated)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy($post, Comment $comment)
    {
        if (auth()->user()->id !== $comment->user_id) throw new GeneralException("Unauthenticate", 401);

        $comment->delete();

        return response("", 204);
    }
}
