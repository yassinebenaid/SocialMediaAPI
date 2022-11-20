<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralException;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct(
        protected PostRepository $postRepo
    ) {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PostResource::collection(Post::with(["user", "likes"])->where("event", "!=", "in_group")->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        $created = $this->postRepo->create($request);

        return response()->json([
            "success" => true,
            "message" => "post created successfully .",
            "post" => new PostResource($created)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        Post::$scope = "all";
        return response()->json([
            "success" => true,
            "message" => "post created successfully .",
            "post" => new PostResource($post)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $updated = $this->postRepo->update($post, $request);

        return response()->json([
            "success" => true,
            "message" => "post created successfully .",
            "post" => new PostResource($updated)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) throw new GeneralException("Unauthenticated !", 401);

        $this->postRepo->delete($post);
        return response("", 204);
    }

    public function like(Post $post)
    {
        $user_id = auth()->user()->id;
        return $post->likes()->toggle($user_id);
    }
}
