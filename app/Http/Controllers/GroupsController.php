<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneralException;
use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdateGroupCoverRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserRsource;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    public function test()
    {
        return "test";
    }


    public function __construct(
        protected GroupRepository $groupRepo,
        protected PostRepository $postRepo
    ) {
    }

    /**
     * create new group
     *
     * @param CreateGroupRequest $request
     * @return void
     */
    public function createGroup(CreateGroupRequest $request)
    {
        Group::$scope = "all";
        $group =  $this->groupRepo->create($request);
        return new GroupResource($group);
    }

    /**
     * update group information
     *
     * @param UpdateGroupRequest $request
     * @param Group $group
     * @return void
     */
    public function updateGroup(UpdateGroupRequest $request, Group $group)
    {
        return new GroupResource($this->groupRepo->update($group, $request));
    }

    /**
     * update group cover image 
     *
     * @param UpdateGroupCoverRequest $request
     * @param Group $group
     * @return void
     */
    public function updateGroupCover(UpdateGroupCoverRequest $request, Group $group)
    {
        $updated = $this->groupRepo->updateCover($group, $request->cover);
        return $updated;
    }

    /**
     * drop group
     *
     * @param Group $group
     * @return void
     */
    public function dropGroup(Group $group)
    {
        $user = auth()->user()->id;
        if (!$group->isSuperAdmin($user)) throw new GeneralException("you don't have permession to execute this operation, call the creator for more information", 403);

        $this->groupRepo->dropGroup($group);
    }

    /**
     * send group join request
     *
     * @param Group $group
     * @return void
     */
    public function joinToGroup(Group $group)
    {
        return $this->groupRepo->toggleJoin($group);
    }

    /**
     * get all join requests
     *
     * @param Group $group
     * @return void
     */
    public function getJoinRequests(Group $group)
    {
        $joiners =  $this->groupRepo->getJoinRequests($group);

        return UserRsource::collection($joiners);
    }

    /**
     * accept join request
     *
     * @param Group $group
     * @param User $user
     * @return void
     */
    public function acceptJoinRequests(Group $group, User $user)
    {
        $accepted =  $this->groupRepo->acceptJoinRequest($group, $user);

        return response()->json([
            "success" => $accepted
        ]);
    }

    /**
     * refuse join request
     *
     * @param Group $group
     * @param User $user
     * @return void
     */
    public function refuseJoinRequests(Group $group, User $user)
    {
        $refused = $this->groupRepo->refuseJoinRequest($group, $user);

        return response()->json([
            "success" => $refused
        ]);
    }

    /**
     * get All group members
     *
     * @param Group $group
     * @return void
     */
    public function getGroupMembers(Group $group)
    {
        if (!$group->isMember(auth()->user()->id)) throw new GeneralException("Unauthenticated", 403);

        $members =  $group->members()->withPivot("role")->get();

        return UserRsource::collection($members);
    }

    /**
     * upgrade group member to be admin or the opposite
     *
     * @return void
     */
    public function upgradeMember(Group $group, User $member)
    {
        $grade =  $this->groupRepo->UpgradeOrDowngradeMember($group, $member);

        return response()->json([
            "success" => true,
            "grade" => $grade
        ]);
    }

    public function getAllPosts(Group $group)
    {
    }

    public function createPost(StorePostRequest $request, Group $group)
    {
        if (!$group->isMember(auth()->user()->id)) throw new GeneralException("Unauthenticated", 403);

        $post =  $this->postRepo->create($request);
        return new PostResource($post);
    }

    public function getPost(Group $group, Post $post)
    {
        if (!$group->isMember(auth()->user()->id)) throw new GeneralException("Unauthenticated", 403);

        $post = $group->posts()->findOrFail($post->id);

        return new PostResource($post);
    }

    public function updatePost(UpdatePostRequest $request, Group $group, Post $post)
    {
        $post = $this->postRepo->update($post, $request);

        return new PostResource($post);
    }

    public function DeletePost(Group $group, Post $post)
    {
        if (!$group->isMember(auth()->user()->id)) throw new GeneralException("Unauthenticated", 403);

        if ($post->user_id !== auth()->user()->id)  throw new GeneralException("Unauthenticated", 403);

        $this->postRepo->delete($post);

        return response(null, 204);
    }
}
