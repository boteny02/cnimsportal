<?php

namespace App\Enums;

enum AttemptType: string
{
    case FIRST_ATTEMPT = 'FIRST_ATTEMPT';
    case REPEAT = 'REPEAT';
    case CARRYOVER = 'CARRYOVER';
    case RESIT = 'RESIT';
    case SPECIAL_EXAM = 'SPECIAL_EXAM';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_ATTEMPT => 'First Attempt',
            self::REPEAT => 'Repeat Course',
            self::CARRYOVER => 'Carryover',
            self::RESIT => 'Resit Examination',
            self::SPECIAL_EXAM => 'Special Examination',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::FIRST_ATTEMPT => 'sky',
            self::REPEAT => 'amber',
            self::CARRYOVER => 'rose',
            self::RESIT => 'purple',
            self::SPECIAL_EXAM => 'indigo',
        };
    }
}
