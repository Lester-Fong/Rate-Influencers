<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    use HasFactory;

    protected $table = "influencers";
    protected $guarded = [];

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
