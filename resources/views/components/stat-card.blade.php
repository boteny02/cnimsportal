@props(['title', 'value', 'sub' => null, 'icon' => 'sparkles', 'color' => 'blue'])

@php
$colorClasses = match($color) {
    'emerald', 'green' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400',
    'amber', 'yellow' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400',
    'rose', 'red' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400',
    'indigo', 'purple' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400',
    default => 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
};
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-xl border border-zinc-200/80 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900']) }}>
    <div class="flex items-center justify-between">
        <dt class="truncate text-xs font-semibold tracking-wider text-zinc-500 uppercase dark:text-zinc-400">{{ $title }}</dt>
        <div class="rounded-lg p-2 {{ $colorClasses }}">
            {{ $slot }}
        </div>
    </div>
    <dd class="mt-2 text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $value }}</dd>
    @if($sub)
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $sub }}</p>
    @endif
</div>
