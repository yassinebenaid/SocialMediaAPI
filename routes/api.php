<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User Routes
Route::middleware("guest")->group(function () {
    Route::post("register", [AuthController::class, "register"]);
    Route::post("login", [AuthController::class, "login"]);
});

Route::middleware("auth:sanctum")->group(function () {
    Route::apiResource("users", UserController::class);
    Route::post("users/{user}/profile_image", [UserController::class, "updateProfileImage"]);
});

// Post Routes
Route::middleware("auth:sanctum")->group(function () {


    Route::apiResource("posts", PostController::class);
    Route::post("posts/{post}/like", [PostController::class, "like"]);

    // Post Comments Routes
    Route::prefix("posts/{post}/comments")->group(function () {
        Route::get("/", [CommentController::class, "index"]);
        Route::get("/{comment}", [CommentController::class, "show"]);
        Route::post("/", [CommentController::class, "store"]);
        Route::match(["PUT", "PATCH"], "/{comment}", [CommentController::class, "update"]);
        Route::delete("/{comment}", [CommentController::class, "destroy"]);
    });
});

// bookmarks
Route::middleware("auth:sanctum")->group(function () {
    Route::get("bookmark", [UserController::class, "GetBookmarks"]);
    Route::post("bookmark/{post}", [UserController::class, "ToggleBookmark"]);
});

// Friends Routes
Route::middleware("auth:sanctum")->group(function () {
    Route::get("friends/requests", [UserController::class, "friendRequests"]);
    Route::post("friends/requests/toggle/{user}", [UserController::class, "toggleFriendRequests"]);
    Route::post("friends/requests/{sender}/accept", [UserController::class, "acceptFriendRequest"]);
    Route::post("friends/requests/{sender}/deny", [UserController::class, "denyFriendRequest"]);
});


// Groups Routes
Route::middleware("auth:sanctum")->group(function () {
    Route::post("groups", [GroupsController::class, "createGroup"]);
    Route::match(["PATCH", "PUT"], "groups/{group}", [GroupsController::class, "updateGroup"]);
    Route::post("groups/{group}/cover", [GroupsController::class, "updateGroupCover"]);
    Route::delete("groups/{group}", [GroupsController::class, "dropGroup"]);

    Route::post("groups/{group}/join", [GroupsController::class, "joinToGroup"]);
    Route::get("groups/{group}/requests", [GroupsController::class, "getJoinRequests"]);
    Route::post("groups/{group}/requests/{user}/accept", [GroupsController::class, "acceptJoinRequests"]);
    Route::post("groups/{group}/requests/{user}/deny", [GroupsController::class, "refuseJoinRequests"]);

    // group members
    Route::get("groups/{group}/members", [GroupsController::class, "getGroupMembers"]);
    Route::post("groups/{group}/members/{member}/upgrade", [GroupsController::class, "upgradeMember"]);

    // group posts
    Route::get("groups/{group}/posts", [GroupsController::class, "getAllPosts"]);
    Route::get("groups/{group}/posts/{post}", [GroupsController::class, "getPost"]);
    Route::post("groups/{group}/posts", [GroupsController::class, "createPost"]);
    Route::match(["PUT", "PATCH"], "groups/{group}/posts/{post}", [GroupsController::class, "updatePost"]);
    Route::delete("groups/{group}/posts/{post}", [GroupsController::class, "deletePost"]);
});

Route::middleware("auth:sanctum")->group(function () {
    Route::apiResource("story", StoryController::class);
});
