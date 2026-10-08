@props(['type' => 'info'])

@php
$classes = match($type) {
    'success', 'approved', 'active', 'paid', 'competent', 'independent' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950/40 dark:text-emerald-400 dark:ring-emerald-500/20',
    'warning', 'pending', 'under_review', 'supervised', 'partially_paid', 'submitted' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/40 dark:text-amber-400 dark:ring-amber-500/20',
    'danger', 'rejected', 'failed', 'withdrawn', 'probation', 'unpaid' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-950/40 dark:text-rose-400 dark:ring-rose-500/20',
    'info', 'shortlisted', 'observed', 'assisted' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-950/40 dark:text-sky-400 dark:ring-sky-500/20',
    'purple', 'processed', 'verified' => 'bg-purple-50 text-purple-700 ring-purple-600/20 dark:bg-purple-950/40 dark:text-purple-400 dark:ring-purple-500/20',
    default => 'bg-zinc-100 text-zinc-700 ring-zinc-500/20 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-600/20',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-x-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' . $classes]) }}>
    <span class="size-1.5 rounded-full bg-current"></span>
    {{ $slot }}
</span>
