<?php

namespace App\Models;

use App\Enums\AttemptType;
use App\Enums\ResultStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'course_offering_id',
        'academic_session_id',
        'semester',
        'attempt_number',
        'attempt_type',
        'ca_score',
        'exam_score',
        'total_score',
        'grade',
        'grade_point',
        'credit_unit',
        'quality_point',
        'credit_points',
        'status',
        'submitted_by',
        'verified_by',
        'approved_by',
        'published_at',
        'correction_reason',
        'correction_requested_by',
        'correction_requested_at',
    ];

    protected $casts = [
        'status' => ResultStatus::class,
        'attempt_type' => AttemptType::class,
        'attempt_number' => 'integer',
        'semester' => 'integer',
        'ca_score' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'credit_unit' => 'integer',
        'quality_point' => 'decimal:2',
        'credit_points' => 'decimal:2',
        'published_at' => 'datetime',
        'correction_requested_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function correctionRequestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'correction_requested_by');
    }

    public function assessmentScores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'course_id', 'course_id')
            ->where('student_id', $this->student_id)
            ->where('academic_session_id', $this->academic_session_id)
            ->where('semester', $this->semester);
    }

    public function isPublished(): bool
    {
        return $this->status === ResultStatus::PUBLISHED;
    }

    public function isFirstAttempt(): bool
    {
        return $this->attempt_type === AttemptType::FIRST_ATTEMPT;
    }

    public function isCarryover(): bool
    {
        return in_array($this->attempt_type, [AttemptType::CARRYOVER, AttemptType::REPEAT]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ResultStatus::PUBLISHED->value);
    }

    public function scopeForSession(Builder $query, int $sessionId): Builder
    {
        return $query->where('academic_session_id', $sessionId);
    }
}
