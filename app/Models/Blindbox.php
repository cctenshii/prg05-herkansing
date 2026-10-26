<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blindbox extends Model
{
    protected $fillable = [
        'name',
        'series_id',
        'summary',
        'image',
        'public', // allow mass assignment if needed
        'created_by',
    ];

    public function series(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}

