<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalLogbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'clinical_posting_id',
        'procedure_id',
        'procedure_date',
        'patient_reference_code',
        'competency_level',
        'student_reflection',
        'supervisor_id',
        'status',
        'supervisor_remarks',
        'verified_at',
    ];

    protected $casts = [
        'procedure_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function posting(): BelongsTo
    {
        return $this->belongsTo(ClinicalPosting::class, 'clinical_posting_id');
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(ClinicalProcedure::class, 'procedure_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
