<x-guest-layout>

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Create an account</h2>
        <p class="mt-1 text-sm text-slate-500">Get started with Working Paper today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                {{ __('Full name') }}
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 @error('name') border-red-400 bg-red-50 @enderror" placeholder="Jane Smith">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                {{ __('Email address') }}
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 @error('email') border-red-400 bg-red-50 @enderror" placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                {{ __('Password') }}
            </label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 @error('password') border-red-400 bg-red-50 @enderror" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                {{ __('Confirm password') }}
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 @error('password_confirmation') border-red-400 bg-red-50 @enderror" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <!-- Submit -->
        <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 mt-2">
            {{ __('Create account') }}
        </button>

        <!-- Already registered -->
        <p class="text-center text-sm text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                {{ __('Sign in') }}
            </a>
        </p>

    </form>

</x-guest-layout>