<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Administrator extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = "administrators";

    protected $fillable = [
        "name",
        "email",
        "password"
    ];

    protected $hidden = [
        "password",
        "remember_token"
    ];


}
