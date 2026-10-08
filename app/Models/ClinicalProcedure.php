<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalProcedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'category',
        'description',
        'minimum_required_count',
    ];

    public function logbooks(): HasMany
    {
        return $this->hasMany(ClinicalLogbook::class, 'procedure_id');
    }
}
