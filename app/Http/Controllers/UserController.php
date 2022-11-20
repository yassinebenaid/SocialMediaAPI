<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralException;
use App\Http\Requests\UpdateProfileImageRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserRsource;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepo
    ) {
    }

    public function index()
    {
        return UserRsource::collection(User::paginate());
    }

    public function store()
    {
        return response(null, 405);
    }
    public function destroy()
    {
        return response(null, 405);
    }

    /**
     * get user profile
     *
     * @param User $user
     * @return void
     */
    public function show(User $user)
    {
        return new ProfileResource($user);
    }

    /**
     * update profile info
     *
     * @param UpdateProfileRequest $request
     * @param User $user
     * @return void
     */
    public function update(UpdateProfileRequest $request, User $user)
    {
        $updated = $this->userRepo->update($user, $request);
        return new UserRsource($updated);
    }

    /**
     *  since php doesn't support transporting images using put or patch methods, we use post to update profile image 
     *
     * @param UpdateProfileImageRequest $request
     * @param User $user
     * @return void
     */
    public function updateProfileImage(UpdateProfileImageRequest $request, User $user)
    {
        $imagePath =  $this->userRepo->updateProfileImage($user, $request->profile_image);

        return response()->json([
            "success" => true,
            "message" => "profile image has been updated successfully .",
            "new_path" => env("APP_URL") . $imagePath,
        ], 200);
    }




    /**
     * get all bookmarked posts
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function GetBookmarks()
    {
        $bookmark = auth()->user()->bookmark;
        return PostResource::collection($bookmark);
    }

    /**
     * add post to bookmark list if not already exists, otherwise remove it
     *
     * @param Post $post
     * @return array 
     */
    public function ToggleBookmark(Post $post)
    {
        return  $this->userRepo->ToggleBookmark($post);
    }

    /**
     * get all friend requests
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function friendRequests()
    {
        $requests = $this->userRepo->getFriendRequests();
        return UserRsource::collection($requests);
    }

    /**
     * send friend requests if not already sent , otherwise remove the request
     *
     * @param Request $request
     * @return array
     */
    public function toggleFriendRequests(User $user)
    {
        // if the user try to send friend request to himself throw error
        if ((int)auth()->user()->id === (int)$user->id) throw new GeneralException("Couldn't send friend request to yourself !", 400);

        return $this->userRepo->sendOrRestoreFriendRequests($user);
    }

    /**
     * accept friend request
     *
     * @param User $sender
     * @return bool
     */
    public function acceptFriendRequest(User $sender)
    {
        // if the user try to accept  friend request from himself throw error
        if ((int)auth()->user()->id === (int)$sender->id) throw new GeneralException("Bad request, Impossible accepting a request from yourself !", 400);

        return $this->userRepo->acceptFriendRequest($sender);
    }

    /**
     * deny friend request
     *
     * @param User $sender
     * @return bool
     */
    public function denyFriendRequest(User $sender)
    {
        // if the user try to refuse  friend request from himself throw error
        if ((int)auth()->user()->id === (int)$sender->id) throw new GeneralException("Bad request, Impossible refusing a request from yourself !", 400);

        return $this->userRepo->refuseFriendRequest($sender);
    }
}
