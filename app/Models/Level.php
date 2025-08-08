<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Level extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'order'
    ];

    /**
     * Get the classes for this level.
     */
    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    /**
     * Scope to get levels in order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
