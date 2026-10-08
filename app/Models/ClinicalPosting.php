<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'programme_id',
        'academic_session_id',
        'level',
        'facility_id',
        'ward_id',
        'supervisor_id',
        'start_date',
        'end_date',
        'max_capacity',
        'learning_objectives',
        'status',
    ];

    protected $casts = [
        'level' => 'integer',
        'max_capacity' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(ClinicalFacility::class, 'facility_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(ClinicalWard::class, 'ward_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'clinical_posting_students')
            ->withPivot('attendance_rate', 'performance_rating', 'supervisor_comments')
            ->withTimestamps();
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(ClinicalLogbook::class);
    }
}
