<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Meta_tag;



class Pages extends Model
{
    protected $fillable = ['page'];


     public function metatags(): HasMany
    {
        return $this->hasMany(Meta_tag::class, 'page_id');
    }
}
