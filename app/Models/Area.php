<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'area_id');
    }
    protected $fillable = [
        'name',
    ];
}