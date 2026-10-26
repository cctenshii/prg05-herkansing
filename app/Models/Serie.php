<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    public function blindbox()
    {
        return $this->hasMany(Blindbox::class);
    }
}
