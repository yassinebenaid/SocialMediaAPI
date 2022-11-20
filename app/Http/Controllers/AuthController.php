<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralException;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserRsource;
use App\Models\User;
use App\Repositories\UserRepository;


class AuthController extends Controller
{
    public function __construct(
        protected UserRepository $userRepo
    ) {
        /** set the user scope to all , so that Userresource will display everything about user         */
    }

    /**
     * register user
     *
     * @param RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        $created = $this->userRepo->create($request);

        return response()->json([
            "success" => true,
            "message" => "Account has been created successfully .",
            "data" => new UserRsource($created)
        ]);
    }

    /**
     * log user in
     *
     * @param LoginUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws GeneralException
     */
    public function login(LoginUserRequest $request)
    {
        if (auth()->attempt($request->only("email", "password"))) {

            /** @var \App\Models\User $user */
            $user = auth()->user();
            $user->accessToken = $user->createToken("api_access_token_for_$user->email")->plainTextToken;


            return response()->json([
                "success" => true,
                "message" => "Account has been created successfully .",
                "data" => new UserRsource($user)
            ]);
        }

        throw new GeneralException("we couldn't match your credentials ! ", 422);
    }
}
