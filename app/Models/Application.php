<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_number',
        'access_code',
        'programme_id',
        'academic_session_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'state_of_origin',
        'lga',
        'address',
        'passport_photo',
        'secondary_school',
        'secondary_school_sitting_1',
        'secondary_school_sitting_2',
        'graduation_year',
        'o_level_sittings',
        'o_level_sitting_1',
        'o_level_sitting_2',
        'o_level_verified',
        'o_level_credits_count',
        'previous_qualification',
        'jamb_reg_number',
        'jamb_score',
        'jamb_subjects',
        'application_fee_paid',
        'application_fee_paid_at',
        'entrance_exam_invited',
        'entrance_exam_date',
        'entrance_exam_venue',
        'entrance_exam_seat_number',
        'entrance_exam_invited_at',
        'entrance_exam_score',
        'entrance_exam_remarks',
        'entrance_exam_scored_at',
        'entrance_exam_scored_by',
        'admission_offered_at',
        'acceptance_deadline',
        'admission_letter_ref',
        'acceptance_fee_paid',
        'acceptance_fee_paid_at',
        'matriculated_at',
        'status',
        'screening_score',
        'screening_remarks',
        'screened_by',
        'submitted_at',
        'decided_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'o_level_sittings' => 'integer',
        'o_level_sitting_1' => 'array',
        'o_level_sitting_2' => 'array',
        'o_level_verified' => 'boolean',
        'o_level_credits_count' => 'integer',
        'jamb_score' => 'integer',
        'jamb_subjects' => 'array',
        'application_fee_paid' => 'boolean',
        'application_fee_paid_at' => 'datetime',
        'entrance_exam_invited' => 'boolean',
        'entrance_exam_date' => 'datetime',
        'entrance_exam_invited_at' => 'datetime',
        'entrance_exam_score' => 'decimal:2',
        'entrance_exam_scored_at' => 'datetime',
        'admission_offered_at' => 'datetime',
        'acceptance_deadline' => 'date',
        'acceptance_fee_paid' => 'boolean',
        'acceptance_fee_paid_at' => 'datetime',
        'matriculated_at' => 'datetime',
        'screening_score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function screener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    public function examScorer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entrance_exam_scored_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getApplicationFeeInvoiceAttribute(): ?Invoice
    {
        return $this->invoices()->where('invoice_type', 'application_fee')->first();
    }

    public function getAcceptanceFeeInvoiceAttribute(): ?Invoice
    {
        return $this->invoices()->where('invoice_type', 'acceptance_fee')->first();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
