<?php

namespace App\Livewire\Public;

use App\Models\Student;
use Livewire\Component;

class CertificateVerification extends Component
{
    public string $student_number = '';

    public ?Student $student = null;

    public bool $searched = false;

    public function verify(): void
    {
        $this->validate([
            'student_number' => 'required|string|min:5',
        ]);

        $this->searched = true;
        $this->student = Student::where('student_number', trim($this->student_number))
            ->with(['programme', 'department', 'currentLevel', 'entrySession'])
            ->first();
    }

    public function render()
    {
        return view('livewire.public.certificate-verification')
            ->layout('layouts.institutional', ['title' => 'Certificate & Student Verification']);
    }
}
