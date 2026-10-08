<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialClearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_session_id',
        'semester',
        'is_cleared',
        'clearance_type',
        'cleared_by',
        'cleared_at',
        'is_overridden',
        'overridden_by',
        'override_reason',
        'overridden_at',
        'remarks',
    ];

    protected $casts = [
        'semester' => 'integer',
        'is_cleared' => 'boolean',
        'cleared_at' => 'datetime',
        'is_overridden' => 'boolean',
        'overridden_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function clearedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }

    public function overriddenByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'overridden_by');
    }
}
