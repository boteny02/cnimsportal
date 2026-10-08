<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSemesterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_session_id',
        'semester',
        'level',
        'credits_registered',
        'credits_earned',
        'quality_points',
        'gpa',
        'cgpa',
        'academic_standing',
        'is_published',
    ];

    protected $casts = [
        'semester' => 'integer',
        'level' => 'integer',
        'credits_registered' => 'integer',
        'credits_earned' => 'integer',
        'quality_points' => 'decimal:2',
        'gpa' => 'decimal:2',
        'cgpa' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
