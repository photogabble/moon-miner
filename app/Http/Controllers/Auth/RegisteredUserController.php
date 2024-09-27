<?php

namespace App\Http\Controllers\Auth;

use App\Models\Ship;
use App\Helpers\Languages;
use App\Actions\SpawnStarterShip;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'locale' => app()->getLocale(),
            'locales' => Languages::listAvailable(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (config('app.account_creation_closed')) {
            return redirect()->back()
                ->withErrors(__('new.l_new_closed_message'));
        }

        $validLanguages = implode(',', Languages::listAvailableKeys());
        $request->validate([
            'character_name' => ['required', 'string', 'max:20', 'unique:'.User::class.',name'],
            'ship_name' => ['required', 'string', 'max:20', 'unique:'.Ship::class.',name'],
            'lang' => ['string', 'in:'.$validLanguages],
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->get('character_name'),
            'locale' => $request->get('locale', config('app.locale')),
            'turns' => config('game.start_turns'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        (new SpawnStarterShip($user))->spawn($request->get('ship_name'));

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('navicom', absolute: false));
    }
}
