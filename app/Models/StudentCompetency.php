<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCompetency extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'skill_id',
        'current_level',
        'assessed_by',
        'assessed_at',
        'remarks',
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(CompetencySkill::class, 'skill_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}
