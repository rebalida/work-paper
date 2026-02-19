<x-guest-layout>

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome back</h2>
        <p class="mt-1 text-sm text-slate-500">Sign in to your Working Paper account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                {{ __('Email address') }}
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 @error('email') border-red-400 bg-red-50 @enderror" placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    {{ __('Password') }}
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 @error('password') border-red-400 bg-red-50 @enderror" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <label for="remember_me" class="text-sm text-slate-600">{{ __('Keep me signed in') }}</label>
        </div>

        <!-- Submit -->
        <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 mt-2">
            {{ __('Sign in') }}
        </button>
    </form>

</x-guest-layout>