<div class="mx-auto max-w-md py-8">
    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-gradient-to-r from-sky-800 to-teal-700 p-6 text-white text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-white/20 text-white font-bold text-xl mb-3 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z" />
                </svg>
            </div>
            <h2 class="text-xl font-black tracking-tight">Applicant Portal Access</h2>
            <p class="text-xs text-sky-100 mt-1">Sign in using your Application Number to access your admission desk</p>
        </div>

        <!-- Form -->
        <form wire:submit.prevent="login" class="p-6 space-y-4">
            @if(session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300">
                    {{ session('error') }}
                </div>
            @endif

            <div>
                <label for="identifier" class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                    Application Number or Email:
                </label>
                <div class="relative">
                    <input type="text" 
                           id="identifier" 
                           wire:model.defer="identifier"
                           placeholder="e.g. APP/2026/00101 or applicant@cnims.edu.ng"
                           class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3.5 py-2.5 text-xs text-zinc-900 focus:border-sky-500 focus:bg-white focus:outline-hidden dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                </div>
                @error('identifier')
                    <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold text-zinc-700 dark:text-zinc-300">
                        Password or Access Passcode:
                    </label>
                </div>
                <input type="password" 
                       id="password" 
                       wire:model.defer="password"
                       placeholder="••••••••"
                       class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3.5 py-2.5 text-xs text-zinc-900 focus:border-sky-500 focus:bg-white focus:outline-hidden dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                @error('password')
                    <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-zinc-600 dark:text-zinc-400">
                    <input type="checkbox" wire:model.defer="remember" class="rounded text-sky-600">
                    <span>Remember Application on this device</span>
                </label>
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full rounded-xl bg-sky-700 py-3 text-xs font-bold text-white shadow-md hover:bg-sky-600 cursor-pointer transition flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="login">Access Applicant Dashboard →</span>
                <span wire:loading wire:target="login">Authenticating Application...</span>
            </button>

            <!-- Quick Demo Helper Note -->
            <div class="rounded-xl bg-sky-50 p-3 text-center text-[11px] text-sky-900 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                <p class="font-semibold">Demo Evaluation Credentials:</p>
                <p class="mt-0.5">App Number: <strong class="font-mono">APP/2026/00101</strong></p>
                <p>Passcode / Password: <strong class="font-mono">password123</strong></p>
            </div>

            <div class="pt-2 text-center text-xs text-zinc-500">
                Haven't applied yet? 
                <a href="{{ route('public.apply') }}" class="font-bold text-sky-600 hover:text-sky-500 ml-1">Start 2025/2026 Application →</a>
            </div>
        </form>
    </div>
</div>
