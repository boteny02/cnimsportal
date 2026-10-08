<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <title>{{ $title ?? 'CNIMS - College of Nursing Information Management System' }}</title>
</head>
<body class="min-h-screen bg-zinc-50 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100 flex flex-col"
      x-data="{ sidebarOpen: false }">

    <!-- Top Role / Persona Switcher for Instant Evaluation & Testing -->
    <x-role-switcher />

    <!-- Application Shell with Side Navigation -->
    <div class="flex flex-1 min-h-0">

        <!-- ============================================================== -->
        <!-- DESKTOP SIDE NAVIGATION (Fixed Sticky Left Sidebar)            -->
        <!-- ============================================================== -->
        <aside class="hidden lg:flex lg:w-72 lg:shrink-0 lg:flex-col lg:sticky lg:top-0 lg:h-screen border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 z-30 shadow-xs">
            @include('partials.sidebar-nav')
        </aside>

        <!-- ============================================================== -->
        <!-- MOBILE SIDE NAVIGATION DRAWER (Slide-over Overlay)             -->
        <!-- ============================================================== -->
        <div x-show="sidebarOpen" 
             x-cloak
             class="relative z-50 lg:hidden"
             role="dialog" 
             aria-modal="true">
            <!-- Backdrop -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false" 
                 class="fixed inset-0 bg-zinc-950/70 backdrop-blur-xs"></div>

            <!-- Slide-out Drawer Panel -->
            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white dark:bg-zinc-900 shadow-2xl">
                    
                    <!-- Close Drawer Button -->
                    <div class="absolute right-0 top-0 -mr-12 pt-4">
                        <button type="button" @click="sidebarOpen = false" class="rounded-lg p-1.5 text-white hover:bg-white/10 transition">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @include('partials.sidebar-nav')
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MAIN CONTENT WORKSPACE                                         -->
        <!-- ============================================================== -->
        <div class="flex flex-1 flex-col min-w-0 min-h-screen">
            
            <!-- Sticky Top Workspace Header Bar -->
            <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center justify-between border-b border-zinc-200 bg-white/95 px-4 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/95 sm:px-6 lg:px-8">
                
                <!-- Left: Hamburger toggle + Context title -->
                <div class="flex items-center gap-3">
                    <button type="button" 
                            @click="sidebarOpen = true" 
                            class="lg:hidden p-2 rounded-lg text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <h1 class="text-sm font-bold tracking-tight text-zinc-900 dark:text-white sm:text-base">
                            {{ $title ?? 'College of Nursing & Midwifery Portal' }}
                        </h1>
                        @if(request()->is('admin*'))
                            <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300 hidden sm:inline-block">Staff Workspace</span>
                        @elseif(request()->is('student*'))
                            <span class="rounded bg-teal-100 px-2 py-0.5 text-[10px] font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-300 hidden sm:inline-block">Student Desk</span>
                        @else
                            <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 hidden sm:inline-block">Public Portal</span>
                        @endif
                    </div>
                </div>

                <!-- Right: Active status, view switchers & profile -->
                <div class="flex items-center gap-3">
                    @auth
                        @if(request()->is('admin*'))
                            <a href="{{ route('student.dashboard') }}" class="hidden md:inline-flex items-center gap-1.5 rounded-lg border border-teal-200 bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-700 hover:bg-teal-100 dark:border-teal-800 dark:bg-teal-950/40 dark:text-teal-300 transition">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                Student View
                            </a>
                        @elseif(request()->is('student*'))
                            <a href="{{ route('admin.dashboard') }}" class="hidden md:inline-flex items-center gap-1.5 rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-100 dark:border-sky-800 dark:bg-sky-950/40 dark:text-sky-300 transition">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Staff View
                            </a>
                        @endif

                        <div class="flex items-center gap-2 pl-2">
                            <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-200 hidden sm:inline">{{ auth()->user()->name }}</span>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            Log In
                        </a>
                        <a href="{{ route('public.apply') }}" class="rounded-lg bg-sky-700 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-sky-600 shadow-xs">
                            Apply Now
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Flash Session Alerts -->
            @if(session('success'))
                <div class="mx-auto mt-4 w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <svg class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-auto mt-4 w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <svg class="size-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Page Content Slot -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Institutional Footer -->
            <footer class="mt-auto border-t border-zinc-200 bg-white py-6 text-xs text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <div class="size-6 rounded bg-sky-600 text-white flex items-center justify-center font-bold text-xs">C</div>
                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">College of Nursing Information Management System</span>
                            <span class="text-zinc-400 hidden sm:inline">|</span>
                            <span class="hidden sm:inline">Accredited by NMCN</span>
                        </div>
                        <div>
                            &copy; {{ date('Y') }} CNIMS Portal • Laravel + Livewire + Side Navigation.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @fluxScripts
</body>
</html>
