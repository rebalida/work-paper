<nav class="flex-1 overflow-y-auto py-4">
    <div class="px-4 space-y-2">
        <a href="{{ route('dashboard') }}" 
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Dashboard
        </a>

        <a href="{{ route('profile.edit') }}" 
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            {{ __('Profile Settings') }}
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-8 pt-4 border-t border-slate-800">
            @csrf
            <button type="submit" class="w-full group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-300 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-slate-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</nav>