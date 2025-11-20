<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


    class Meta_tag extends Model
{
    protected $fillable = [
        'page_id',
        'page',
        'title',
        'description',
        'keywords',
        'canonical_url'
    ];

    public function page()
    {
        return $this->belongsTo(Pages::class, 'page_id');
    }
}


