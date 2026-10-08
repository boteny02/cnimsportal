<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'address',
        'contact_person',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function wards(): HasMany
    {
        return $this->hasMany(ClinicalWard::class);
    }

    public function postings(): HasMany
    {
        return $this->hasMany(ClinicalPosting::class, 'facility_id');
    }
}
