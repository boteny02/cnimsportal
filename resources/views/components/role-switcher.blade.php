@php
$currentUser = auth()->user();
$demoUsers = [
    ['email' => 'admin@cnims.edu.ng', 'role' => 'Super Admin', 'name' => 'Prof. Fatima Bello'],
    ['email' => 'institutional.admin@cnims.edu.ng', 'role' => 'Admin', 'name' => 'Dr. Aliyu Mohammed'],
    ['email' => 'registrar@cnims.edu.ng', 'role' => 'Registrar', 'name' => 'Dr. Amina Abubakar'],
    ['email' => 'dean@cnims.edu.ng', 'role' => 'Dean', 'name' => 'Prof. Sani Garba'],
    ['email' => 'hod.nursing@cnims.edu.ng', 'role' => 'HOD', 'name' => 'Dr. Emmanuel Adeyemi'],
    ['email' => 'academic.officer@cnims.edu.ng', 'role' => 'Academic Off.', 'name' => 'Mr. Victor Olawale'],
    ['email' => 'lecturer@cnims.edu.ng', 'role' => 'Lecturer', 'name' => 'Mrs. Grace Danladi'],
    ['email' => 'clinical.coordinator@cnims.edu.ng', 'role' => 'Clin. Coord.', 'name' => 'Nurse Hauwa Sambo'],
    ['email' => 'clinical.instructor@cnims.edu.ng', 'role' => 'Clin. Preceptor', 'name' => 'Nurse Ibrahim Yakubu'],
    ['email' => 'exam.officer@cnims.edu.ng', 'role' => 'Exam Officer', 'name' => 'Mr. Chidi Eze'],
    ['email' => 'finance.officer@cnims.edu.ng', 'role' => 'Finance Off.', 'name' => 'Mr. Tunde Lawal'],
    ['email' => 'bursar@cnims.edu.ng', 'role' => 'Bursar', 'name' => 'Mrs. Kemi Ojo'],
    ['email' => 'student@cnims.edu.ng', 'role' => 'Student Nurse', 'name' => 'Jane Okon (CON/2026/00101)'],
    ['email' => 'applicant@cnims.edu.ng', 'role' => 'Applicant', 'name' => 'Blessing Danjuma (APP/2026/00101)'],
];
@endphp

<div class="border-b border-sky-200 bg-sky-50/90 px-4 py-2 text-xs text-sky-900 backdrop-blur-xs dark:border-sky-900/50 dark:bg-sky-950/70 dark:text-sky-200">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="inline-flex size-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-semibold uppercase tracking-wider text-[11px]">Active Persona:</span>
            <span class="rounded bg-sky-100 px-1.5 py-0.5 font-medium text-sky-800 dark:bg-sky-900 dark:text-sky-100">
                {{ $currentUser ? $currentUser->name . ' (' . ($currentUser->roles->pluck('display_name')->first() ?? 'User') . ')' : 'Guest / Public Visitor' }}
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-zinc-500 text-[11px] font-medium mr-1 dark:text-zinc-400">Switch Persona:</span>
            @foreach($demoUsers as $u)
                <form method="POST" action="{{ route('demo.switch-user') }}" class="inline">
                    @csrf
                    <input type="hidden" name="email" value="{{ $u['email'] }}">
                    <button type="submit" 
                        title="{{ $u['name'] }} - {{ $u['role'] }}"
                        class="rounded-md px-2 py-0.5 text-[11px] font-medium transition cursor-pointer {{ $currentUser && $currentUser->email === $u['email'] ? 'bg-sky-700 text-white shadow-xs' : 'bg-white text-zinc-700 hover:bg-sky-100 hover:text-sky-900 border border-zinc-200 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 dark:hover:bg-zinc-700' }}">
                        {{ $u['role'] }}
                    </button>
                </form>
            @endforeach
            @if($currentUser)
                <form method="POST" action="{{ route('logout') }}" class="inline ml-1">
                    @csrf
                    <button type="submit" class="rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-medium text-rose-700 hover:bg-rose-100 cursor-pointer dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                        Logout
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
