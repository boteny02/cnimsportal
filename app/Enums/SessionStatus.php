<?php

namespace App\Enums;

enum SessionStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case RESULT_PROCESSING = 'result_processing';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft (Preparing)',
            self::ACTIVE => 'Active (Instruction & Registration)',
            self::RESULT_PROCESSING => 'Result Processing',
            self::CLOSED => 'Closed (Finalized)',
            self::ARCHIVED => 'Archived (Historical)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'zinc',
            self::ACTIVE => 'emerald',
            self::RESULT_PROCESSING => 'amber',
            self::CLOSED => 'rose',
            self::ARCHIVED => 'purple',
        };
    }

    public function allowsRegistration(): bool
    {
        return $this === self::ACTIVE;
    }

    public function allowsResultProcessing(): bool
    {
        return in_array($this, [self::ACTIVE, self::RESULT_PROCESSING]);
    }

    public function allowsScoreEntry(): bool
    {
        return in_array($this, [self::ACTIVE, self::RESULT_PROCESSING]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::CLOSED, self::ARCHIVED]);
    }
}
