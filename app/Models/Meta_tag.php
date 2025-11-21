<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


    class Meta_tag extends Model
{
    protected $fillable = [
        'page',
        'title',
        'description',
        'keywords',
        'canonical_url'
    ];

    
}


