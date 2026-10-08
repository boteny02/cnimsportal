<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'numeric_level',
    ];

    protected $casts = [
        'numeric_level' => 'integer',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'current_level_id');
    }
}
