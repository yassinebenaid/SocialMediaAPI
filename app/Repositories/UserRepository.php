<?php



namespace App\Repositories;

use App\Events\UserRegistered;
use App\Exceptions\GeneralException;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Repository
{

    /**
     * register new user
     *
     * @param Request $request
     * @return \App\Models\User
     */
    public function create(Request $request)
    {
        try {

            $user = User::create([
                "username" => $request->username,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "birthday" => $request->birthday,
                "gender" => $request->gender ?? "unknown",
                "bio" => $request->bio ?? null,
                "region" => $request->region ?? null,
                "phone_number" => $request->phone_number ?? null,
                "profile_image" => $this->saveImage($request->profile_image, "users") ??  "/resource/images/users/default.png"
            ]);

            $user->accessToken = $user->createToken("api_access_token_for_$user->email")->plainTextToken;

            // 
        } catch (\Throwable $th) {
            throw new GeneralException($th->getMessage(), 422);
        }

        event(new UserRegistered($user));

        return $user;
    }

    /**
     * update profile info
     *
     * @param User $user
     * @param Request $request
     * @return void
     */
    public function update(User $user, Request $request)
    {
        return DB::transaction(function () use ($user, $request) {
            try {
                $user->username     =   $request->username     ??   $user->username;
                $user->email        =   $request->email        ??   $user->email;
                $user->password     =   $request->password     ?    Hash::make($request->password) : $user->password;
                $user->birthday     =   $request->birthday     ??   $user->birthday;
                $user->gender       =   $request->gender       ??   $user->gender;
                $user->bio          =   $request->bio          ??   $user->bio;
                $user->region       =   $request->region       ??   $user->region;
                $user->phone_number =   $request->phone_number ??   $user->phone_number;

                $user->save();

                return $user;
            } catch (\Throwable $th) {
                throw new GeneralException($th->getMessage(), 406);
            }
        });
    }

    /**
     * update profile image
     * 
     * @param User $user
     * @param [type] $image
     * @return void
     */
    public function updateProfileImage(User $user, $image)
    {
        $path = $this->updateImage($user->profile_image, $image, "users");

        $user->update(["profile_image" => $path]);
        $user->posts()->create([
            "type" => "media",
            "image" => $path,
            "event" => "profile_update"
        ]);

        return $path;
    }

    /**
     * add post to bookmark list , is already exists then remove it
     *
     * @param Post $post
     * @return array
     */
    public function ToggleBookmark(Post $post)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return  $user->bookmark()->toggle($post);
    }

    /**
     * get friend requests
     *
     * @return collection
     */
    public function getFriendRequests()
    {
        return  auth()->user()->friendRequests;
    }

    /**
     * add friend request if not exists , otherwise remove it
     *
     * @param [type] $friend
     * @return void
     */
    public function sendOrRestoreFriendRequests($given_user)
    {
        // check if the user was already sent me a friend request
        if ($this->checkIfUserHasNotSentMeRequest($given_user->id)) throw new GeneralException("You can't send request to user who already send you a request !", 406);


        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user->friendRequests(true)->toggle($given_user);
    }

    /**
     * check if the user was already sent me a friend request
     *
     * @param int $user
     * @return bool
     */
    private function checkIfUserHasNotSentMeRequest($user)
    {
        return  $this->getFriendRequests()->contains(function ($value, $key) use ($user) {
            return (int)$value->id === (int)$user;
        });
    }



    /**
     * accept a friend request
     *
     * @param \App\Models\User $sender
     * @return bool
     */
    public function acceptFriendRequest(User $sender)
    {
        // if the user was not sent me any friend request return not found
        $reciever =  $sender->friendRequests(true)->where("reciever_id", auth()->user()->id)->firstOrFail();


        // check if the user is not my friend , if so then throw error
        if ($reciever->isFriendTo($sender)) throw new GeneralException("Bad Request , User already your friend !", 400);


        $submitted = DB::transaction(function () use ($reciever, $sender) {

            try {
                // add user to my friends table
                $reciever->friends()->attach($sender);

                // delete request from requests table since we accepted it
                $sender->friendRequests(true)->detach($reciever);

                return true;

                //
            } catch (\Throwable $th) {

                throw new GeneralException("couldn't accept request ! ", 422);

                return false;
            }
        });

        return $submitted;
    }

    /**
     * refuse a friend request
     *
     * @param User $sender
     * @return void
     */
    public function refuseFriendRequest(User $sender)
    {
        // if the user was not sent me any friend request then throw not found error
        $reciever =  $sender->friendRequests(true)->where("reciever_id", auth()->user()->id)->firstOrFail();

        return DB::transaction(function () use ($sender, $reciever) {
            try {

                $sender->friendRequests(true)->detach($reciever);

                return true;
                // 
            } catch (\Throwable $th) {

                throw new GeneralException("couldn't refuse request !", 422);

                return true;
            }
        });
    }
}
