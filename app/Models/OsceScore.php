<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OsceScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'osce_station_id',
        'student_id',
        'assessor_id',
        'score_awarded',
        'max_possible',
        'rubric_breakdown',
        'examiner_comments',
    ];

    protected $casts = [
        'score_awarded' => 'decimal:2',
        'max_possible' => 'decimal:2',
        'rubric_breakdown' => 'array',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(OsceStation::class, 'osce_station_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }
}
