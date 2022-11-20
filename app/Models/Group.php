<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public static string $scope = "min";

    protected $fillable = [
        "name", "description", "theme", "cover"
    ];

    public function isSuperAdmin(int $user_id)
    {
        return $this->members()->wherePivot("role", "=", "superAdmin")->where("user_id", $user_id)->first()?->exists ?: false;
    }

    public function isAdmin(int $user_id)
    {
        $admin = $this->members()->wherePivot("role", "=", "admin")->where("user_id", $user_id)->first()?->exists ?: false;
        return $admin || $this->isSuperAdmin($user_id);
    }

    public function isMember(int $user_id)
    {
        return $this->members()->where("user_id", $user_id)->first()?->exists ?: false;
    }

    public function members()
    {
        return $this->belongsToMany(User::class, "group_members", "group_id", "user_id");
    }

    public function joinRequests()
    {
        return $this->belongsToMany(User::class, "join_group_requests", "group_id", "user_id");
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, "group_posts", "group_id", "post_id");
    }
}
