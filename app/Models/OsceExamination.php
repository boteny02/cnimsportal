<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OsceExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'programme_id',
        'academic_session_id',
        'exam_date',
        'total_stations',
        'total_possible_score',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'total_stations' => 'integer',
        'total_possible_score' => 'decimal:2',
    ];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function stations(): HasMany
    {
        return $this->hasMany(OsceStation::class);
    }
}
