<?php

namespace App\Enums;

enum ResultStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted_by_lecturer';
    case VERIFIED = 'verified_by_hod';
    case PROCESSED = 'processed_by_exam_officer';
    case APPROVED = 'approved_by_board';
    case PUBLISHED = 'published';
    case CORRECTION_REQUESTED = 'correction_requested';
    case AMENDED = 'amended';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted by Lecturer',
            self::VERIFIED => 'Verified by HOD',
            self::PROCESSED => 'Processed by Exam Officer',
            self::APPROVED => 'Approved by Academic Board',
            self::PUBLISHED => 'Published to Students',
            self::CORRECTION_REQUESTED => 'Correction Requested',
            self::AMENDED => 'Amended Result',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'zinc',
            self::SUBMITTED => 'sky',
            self::VERIFIED => 'indigo',
            self::PROCESSED => 'amber',
            self::APPROVED => 'teal',
            self::PUBLISHED => 'emerald',
            self::CORRECTION_REQUESTED => 'rose',
            self::AMENDED => 'violet',
        };
    }

    public function isModifiableByLecturer(): bool
    {
        return in_array($this, [self::DRAFT, self::CORRECTION_REQUESTED]);
    }
}
