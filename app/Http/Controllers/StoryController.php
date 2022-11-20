<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralException;
use App\Models\Story;
use App\Http\Requests\StoreStoryRequest;
use App\Http\Requests\UpdateStoryRequest;
use App\Http\Resources\StoryResource;
use App\Models\User;
use App\Repositories\StoryRepository;

class StoryController extends Controller
{
    public function __construct(
        protected StoryRepository $storyRepo
    ) {
        $this->storyRepo->deleteExpiredStories();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $friends = auth()->user()->friends;

        if ($friends->count() > 0) $stories = Story::whereBelongsTo($friends)->orWhereBelongsTo(auth()->user())->paginate(8);
        else                       $stories = Story::WhereBelongsTo(auth()->user())->get();



        return StoryResource::collection($stories);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStoryRequest $request)
    {
        $story =  $this->storyRepo->create($request);
        return new StoryResource($story->load('user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function show(Story $story)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($story->user_id != $user->id && !$user->isFriendTo($story->user_id)) throw new GeneralException("Unauthenticated", 401);

        return new StoryResource($story);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStoryRequest  $request
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStoryRequest $request, Story $story)
    {
        $updated  = $this->storyRepo->update($story, $request);

        return new StoryResource($updated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function destroy(Story $story)
    {
        if ($story->user_id != auth()->user()->id) throw new GeneralException("Unauthenticated", 401);
        return $this->storyRepo->delete($story);
    }
}
