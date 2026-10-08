<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OsceStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'osce_examination_id',
        'station_number',
        'title',
        'scenario',
        'allocated_time_minutes',
        'max_score',
        'assessor_id',
    ];

    protected $casts = [
        'station_number' => 'integer',
        'allocated_time_minutes' => 'integer',
        'max_score' => 'decimal:2',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(OsceExamination::class, 'osce_examination_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(OsceStationRubric::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(OsceScore::class);
    }
}
