<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'start_date',
        'end_date',
        'status',
        'is_current',
        'created_by',
        'activated_by',
        'activated_at',
        'closed_by',
        'closed_at',
        'archived_by',
        'archived_at',
    ];

    protected $casts = [
        'status' => SessionStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'activated_at' => 'datetime',
        'closed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function semesters(): HasMany
    {
        return $this->hasMany(AcademicSemester::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(AcademicSemester::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'academic_session_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(StudentResult::class);
    }

    public function courseRegistrations(): HasMany
    {
        return $this->hasMany(StudentCourseRegistration::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function archiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function isActive(): bool
    {
        return $this->status === SessionStatus::ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this->status === SessionStatus::DRAFT;
    }

    public function isResultProcessing(): bool
    {
        return $this->status === SessionStatus::RESULT_PROCESSING;
    }

    public function isClosed(): bool
    {
        return $this->status === SessionStatus::CLOSED;
    }

    public function isArchived(): bool
    {
        return $this->status === SessionStatus::ARCHIVED;
    }

    public function allowsRegistration(): bool
    {
        return $this->status?->allowsRegistration() ?? false;
    }

    public function allowsResultProcessing(): bool
    {
        return $this->status?->allowsResultProcessing() ?? false;
    }

    public function allowsScoreEntry(): bool
    {
        return $this->status?->allowsScoreEntry() ?? false;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SessionStatus::ACTIVE->value);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public static function current(): ?self
    {
        return static::where('is_current', true)->first()
            ?? static::where('status', SessionStatus::ACTIVE->value)->first()
            ?? static::latest('id')->first();
    }
}
