<?php

namespace App\Livewire\Admin\AuditLogs;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogViewer extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('action', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhere('ip_address', 'like', "%{$this->search}%");
            });
        }

        $logs = $query->latest()->paginate(15);

        return view('livewire.admin.audit-logs.audit-log-viewer', [
            'logs' => $logs,
        ])->layout('layouts.institutional', ['title' => 'Institutional Audit Trail & Compliance']);
    }
}
