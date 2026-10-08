<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetencySkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'target_level',
        'rubric_criteria',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CompetencyCategory::class, 'category_id');
    }

    public function studentCompetencies(): HasMany
    {
        return $this->hasMany(StudentCompetency::class, 'skill_id');
    }
}
