<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public static $scope = "min";

    protected $fillable = [
        "user_id", "type", "title", "body", "image", "event"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, "likes", "post_id", "user_id");
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
