<x-app-layout>
    <x-slot:title>Register | Moon Miner</x-slot:title>

    <x-main-panel centered>
        <form method="post" class="border border-ui-orange-500 py-2 px-3 border-x-8 w-1/3 space-y-3">
            @csrf

            <div>
                <x-input-label for="email" :value="__('login.l_login_email')" />
                <x-text-input id="email" type="email" name="email" placeholder='someone@example.com' :value="old('email')" required autofocus autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="ship_name" :value="__('new.l_new_shipname')" />
                <x-text-input id="ship_name" type="text" name="ship_name" :value="old('ship_name')" required />
                <x-input-error :messages="$errors->get('ship_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="character_name" :value="__('new.l_new_pname')" />
                <x-text-input id="character_name" type="text" name="character_name" :value="old('character_name')" required />
                <x-input-error :messages="$errors->get('character_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('login.l_login_pw')" />
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('login.l_login_pw_confirm')" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="locale" :value="__('options.l_opt_lang')" />
                <select id="locale" name="locale">
                    @foreach(\App\Helpers\Languages::listAvailable() as $id => $lang)
                        <option value='{{ $id }}' @if(!old('locale') && app()->getLocale() === $id || old('locale') === $id) selected @endif>{{ $lang['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('locale')" class="mt-2" />
            </div>

            <div style="text-align:center">
                <button class="button green"><span class="shine"></span>{{ __('common.l_submit') }}</button>
                <button type="reset" class="button red"><span class="shine"></span>{{ __('common.l_reset') }}</button>
            </div>
        </form>
    </x-main-panel>

</x-app-layout>
