<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $fillable = [
        'name',
    ];

    public function blindboxes()
    {
        return $this->hasMany(Blindbox::class);
    }
}
