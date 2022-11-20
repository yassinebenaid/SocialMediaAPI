<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        "excerpt", "image", "expired_at"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
