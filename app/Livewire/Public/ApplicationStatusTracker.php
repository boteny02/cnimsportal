<?php

namespace App\Livewire\Public;

use App\Models\Application;
use Livewire\Component;

class ApplicationStatusTracker extends Component
{
    public string $search_query = '';

    public ?Application $application = null;

    public bool $searched = false;

    public function mount(): void
    {
        if (request()->has('ref')) {
            $this->search_query = request()->get('ref');
            $this->checkStatus();
        }
    }

    public function checkStatus(): void
    {
        $this->validate([
            'search_query' => 'required|string|min:4',
        ]);

        $this->searched = true;
        $this->application = Application::where('application_number', trim($this->search_query))
            ->orWhere('email', trim($this->search_query))
            ->with(['programme', 'academicSession'])
            ->first();
    }

    public function render()
    {
        return view('livewire.public.application-status-tracker')
            ->layout('layouts.institutional', ['title' => 'Track Application Status']);
    }
}
