<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourseRegistrationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'course_id',
        'status',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(StudentCourseRegistration::class, 'registration_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
