<?php declare(strict_types=1);
/**
 * Moon Miner, a Free & Opensource (FOSS), web-based 4X space/strategy game forked
 * and based upon Black Nova Traders.
 *
 * @copyright 2024 Simon Dann
 * @copyright 2001-2014 Ron Harwood and the BNT development team
 *
 * @license GNU AGPL version 3.0 or (at your option) any later version.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace App\Http\Controllers\Auth;

use App\Helpers\Languages;
use App\Actions\SpawnStarterShip;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ship;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
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

            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /**
         * The UserObserver will create a new Ship record for this User and
         * handle any other housekeeping tasks required for new User creation.
         * @var User $user
         */
        $user = User::create([
            'name' => $request->get('character_name'),
            'locale' => $request->get('locale', config('app.locale')),
            'turns' => config('game.start_turns'),

            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        (new SpawnStarterShip($user))->spawn($request->get('ship_name'));

        // TODO listen to Login event and update 'last_login' => Carbon::now(),

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
