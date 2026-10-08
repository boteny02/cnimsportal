<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'programme_id',
        'department_id',
        'entry_session_id',
        'current_level_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'address',
        'state_of_origin',
        'lga',
        'blood_group',
        'genotype',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'avatar',
        'status',
        'admitted_at',
        'graduated_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admitted_at' => 'date',
        'graduated_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function entrySession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'entry_session_id');
    }

    public function currentLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'current_level_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function courseRegistrations(): HasMany
    {
        return $this->hasMany(StudentCourseRegistration::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(StudentResult::class);
    }

    public function semesterResults(): HasMany
    {
        return $this->hasMany(StudentSemesterResult::class);
    }

    public function postings(): BelongsToMany
    {
        return $this->belongsToMany(ClinicalPosting::class, 'clinical_posting_students')
            ->withPivot('attendance_rate', 'performance_rating', 'supervisor_comments')
            ->withTimestamps();
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(ClinicalLogbook::class);
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(StudentCompetency::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function financialClearances(): HasMany
    {
        return $this->hasMany(FinancialClearance::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
