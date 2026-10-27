<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blindbox extends Model
{
    protected $fillable = [
        'name',
        'serie_id',
        'summary',
        'image',
        'public',
        'created_by',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    public function serie(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function favoritedBy(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blindbox_user')->withTimestamps();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!empty($this->image)) {
            return asset('storage/' . $this->image);
        }

        $extensions = ['webp', 'png', 'jpg', 'jpeg', 'gif'];

        foreach ($extensions as $extension) {
            $path = public_path("images/{$this->id}.{$extension}");

            if (file_exists($path)) {
                return asset("images/{$this->id}.{$extension}");
            }
        }

        return null;
    }
}

