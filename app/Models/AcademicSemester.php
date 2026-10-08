<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSemester extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_session_id',
        'semester',
        'name',
        'code',
        'sequence',
        'status',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'semester' => 'integer',
        'sequence' => 'integer',
        'is_current' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'academic_semester_id');
    }
}
