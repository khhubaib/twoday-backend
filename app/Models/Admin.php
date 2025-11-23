<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // optional for auth helpers

class Admin extends Authenticatable
{
    protected $fillable = ['name','email','password','api_token'];
    protected $hidden = ['password','api_token'];
    public $timestamps = true;
}
