<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class GroupRepository extends Repository
{
    /**
     * create new Group
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        return DB::transaction(function () use ($request) {

            try {
                $group =  Group::create([
                    "name" => $request->name,
                    "description" => $request->description,
                    "theme" => $request->theme ?? "default",
                    "cover" => $this->saveImage($request->cover, "groups") ?? "default"
                ]);

                // upgrade the creator to be the super admin for the group , which can do anything in the group
                $group->members()->attach(auth()->user(), ["role" => "superAdmin"]);

                return $group;
                // 
            } catch (\Throwable $th) {
                throw new GeneralException($th->getMessage(), 422);
            }
        });
    }

    /**
     * update group info
     *
     * @param Group $group
     * @param Request $request
     * @return void
     */
    public function update(Group $group, Request $request)
    {
        try {
            $group->update([
                "name" => $request->name ?? $group->name,
                "description" => $request->description ?? $group->description,
                "theme" => $request->theme ?? $group->theme
            ]);

            return $group;
            //
        } catch (\Throwable $th) {
            throw new GeneralException("couldn't update the group ! ", 422);
        }
    }

    /**
     * update the group cover image
     *
     * @param Group $group
     * @param [type] $image
     * @return void
     */
    public function updateCover(Group $group, $image)
    {
        $path = $this->updateImage($group->cover, $image, "groups");

        $group->update(["cover" => $path]);

        return $path;
    }

    /**
     * drop a group
     *
     * @param Group $group
     * @return void
     */
    public function dropGroup(Group $group)
    {
        $group->delete();
    }

    /**
     * toggle group join request , if the request is exists remove it ,otherwise add new one
     *
     * @param Group $group
     * @return void
     */
    public function toggleJoin(Group $group)
    {
        $user = auth()->user()->id;

        if ($group->isMember($user)) throw new GeneralException("couldn't send request, you already member in \"$group->name\" !", 400);

        // if the group is public , join the user without sending a request to the admins
        if ($group->isPublic) return $group->members()->attach($user);

        return $group->joinRequests()->toggle($user);
    }

    /**
     * get group join requests, but only the admins can see this list
     *
     * @param Group $group
     * @return void
     */
    public function getJoinRequests(Group $group)
    {
        $user = auth()->user()->id;

        if (!request()->group->isAdmin($user)) throw new GeneralException("Unauthenticated", 403);

        return $group->joinRequests;
    }

    /**
     * accept join request
     *
     * @param Group $group
     * @param User $sender
     * @return void
     */
    public function acceptJoinRequest(Group $group, User $sender)
    {
        if (!$this->hasSentJoinRaquest($group, $sender->id)) throw new GeneralException("couln't find join request on [ $sender->id ]", 404);

        if ($group->isMember($sender->id)) throw new GeneralException("$sender->username is already member on \"$group->name\"", 400);

        return DB::transaction(function () use ($group, $sender) {

            try {
                $group->members()->attach($sender);

                // after join the user, we need to remove the request from the requests table
                $group->joinRequests()->detach($sender);

                return true;
                // 
            } catch (\Throwable $th) {

                throw new GeneralException("couldn't join $sender->username to group \"$group->name\"");
                return false;
            }
        });
    }

    /**
     * refuse a join request
     *
     * @param Group $group
     * @param User $sender
     * @return void
     */
    public function refuseJoinRequest(Group $group, User $sender)
    {
        if (!$this->hasSentJoinRaquest($group, $sender->id)) throw new GeneralException("couln't find join request on [ $sender->id ]", 404);


        return DB::transaction(function () use ($group, $sender) {

            try {

                $group->joinRequests()->detach($sender);

                return true;
                // 
            } catch (\Throwable $th) {

                throw new GeneralException("couldn't refuse $sender->username from group \"$group->name\"");
                return false;
            }
        });
    }

    /**
     * check if the user has sent join request
     *
     * @param [type] $group
     * @param [type] $user_id
     * @return boolean
     */
    public function hasSentJoinRaquest($group, $user_id)
    {
        return $group->joinRequests()->where("id", $user_id)->first()?->exists ?: false;
    }

    /**
     * upgrade a member to be an admin , or if he is an admin , downgrade him to be just a member
     *
     * @param Group $group
     * @param User $member
     * @return void
     */
    public function UpgradeOrDowngradeMember(Group $group, User $member)
    {
        if (!$group->isSuperAdmin(auth()->user()->id)) throw new GeneralException("Unauthenticated", 404);

        if ($group->isSuperAdmin($member->id)) throw new GeneralException("Unable upgrade the creator !", 400);

        if (!$group->isMember($member->id)) throw new GeneralException("Unabled upgrade non-members ! ", 400);

        // if the member is admin , we make it member
        if ($group->isAdmin($member->id)) {

            $group->members()->updateExistingPivot($member->id, ["role" => "member"]);

            return "member";
        }

        $group->members()->updateExistingPivot($member->id, ["role" => "admin"]);

        return "admin";
    }
}
