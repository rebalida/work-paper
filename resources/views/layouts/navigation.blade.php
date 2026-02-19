<nav class="flex-1 overflow-y-auto py-4">
    <div class="px-4 space-y-2">
        <a href="{{ route('dashboard') }}" 
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <x-heroicon-o-squares-2x2 class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
            Dashboard
        </a>

        <a href="{{ route('profile.edit') }}" 
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <x-heroicon-o-user class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
            {{ __('Profile Settings') }}
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-8 pt-4 border-t border-slate-800">
            @csrf
            <button type="submit" class="w-full group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-300 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                <x-heroicon-o-arrow-right-on-rectangle class="mr-3 h-5 w-5 flex-shrink-0 text-slate-400 group-hover:text-red-400" />
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</nav>