<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalWard extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_facility_id',
        'name',
        'unit_code',
        'bed_capacity',
        'student_capacity',
        'ward_in_charge',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(ClinicalFacility::class, 'clinical_facility_id');
    }

    public function postings(): HasMany
    {
        return $this->hasMany(ClinicalPosting::class, 'ward_id');
    }
}
