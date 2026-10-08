<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OsceStationRubric extends Model
{
    use HasFactory;

    protected $fillable = [
        'osce_station_id',
        'criterion',
        'max_score',
    ];

    protected $casts = [
        'max_score' => 'decimal:2',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(OsceStation::class, 'osce_station_id');
    }
}
