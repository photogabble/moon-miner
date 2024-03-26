<x-app-layout>
    <x-slot:title>Login | Moon Miner</x-slot:title>

    <x-main-panel centered>
        <form method="post" class="border border-ui-orange-500 py-2 px-3 border-x-8 w-1/3 space-y-3">
            @csrf
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div>
                <x-input-label for="email" :value="__('login.l_login_email')" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" :value="__('login.l_login_pw')" />
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <button>{{ __('login.l_login_title') }}</button>
        </form>
    </x-main-panel>
</x-app-layout>
