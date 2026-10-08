<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseOffering extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'academic_session_id',
        'academic_semester_id',
        'level',
        'department_id',
        'lecturer_id',
        'capacity',
        'status',
    ];

    protected $casts = [
        'level' => 'integer',
        'capacity' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function academicSemester(): BelongsTo
    {
        return $this->belongsTo(AcademicSemester::class, 'academic_semester_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(StudentResult::class, 'course_offering_id');
    }

    public function assessmentScores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'course_offering_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $code = $this->course?->code ?? 'N/A';
        $session = $this->academicSession?->name ?? '';
        $semester = $this->academicSemester?->name ?? "Semester {$this->academicSemester?->semester}";

        return "{$code} ({$session} - {$semester})";
    }
}
