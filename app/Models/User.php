<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Hamcrest\Type\IsInteger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        "gender",
        "birthday",
        "bio",
        "phone_number",
        "region",
        "profile_image"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function bookmark()
    {
        return $this->belongsToMany(Post::class, "bookmarks", "user_id", "post_id");
    }

    public function friendRequests($reverseMod = false)
    {
        if ($reverseMod) return $this->belongsToMany(User::class, "friend_requests", "sender_id", "reciever_id");

        return $this->belongsToMany(User::class, "friend_requests", "reciever_id", "sender_id");
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, "friends", "user_id", "friend_id");
    }

    public function getFriendsAttribute()
    {
        return $this->friends()->get()->merge($this->friends_2()->get());
    }

    public function friends_2()
    {
        return $this->belongsToMany(User::class, "friends", "friend_id", "user_id");
    }

    public function isFriendTo($user)
    {
        if (is_int($user))  return  $this->friends()->get()->merge($this->friends_2()->get())->contains(function ($value, $key) use ($user) {
            return (int)$value->id === (int)$user;
        });

        return  $this->friends()->get()->merge($this->friends_2()->get())->contains(function ($value, $key) use ($user) {
            return (int)$value->id === (int)$user->id;
        });
    }

    /**
     * check if the given user is my friend
     *
     * @param \App\Models\User $user
     * @return bool
     */
    private function check($user)
    {
        return  $this->friends()->get()->contains(function ($value, $key) use ($user) {
            return (int)$value->id === (int)$user->id;
        });
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, "group_members", "user_id", "group_id");
    }

    public function story()
    {
        return $this->hasOne(Story::class);
    }
}
