<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StoryRepository extends Repository
{
    public function create(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            // first we need to delete old story , so that the user can only have one story
            $user->story()->get()->map(function ($el) {

                if (is_file(public_path() . $el->image)) {
                    File::delete(public_path() . $el->image);
                }

                $el->delete();
            });



            $user->story()->delete();

            $user->story()->create([
                "excerpt" => $request->except,
                "image" => $this->saveImage($request->image, "stories"),
                "expired_at" => now()->addMinutes(2)
            ]);

            return $user->story;
            // 
        } catch (\Throwable $th) {
            throw new GeneralException("couldn't create story !", 422);
        }
    }

    public function update(Story $story, Request $request)
    {
        $story->update([
            "excerpt" => $request->excerpt ?? $story->excerpt
        ]);

        return $story;
    }

    public function delete(Story $story)
    {
        return $story->delete();
    }

    public function deleteExpiredStories()
    {
        $stories = Story::where("expired_at", "<=", now())->get();

        foreach ($stories as $story) {
            File::delete(public_path() . $story->image);
        }

        Story::where("expired_at", "<=", now())->delete();
    }
}
// 