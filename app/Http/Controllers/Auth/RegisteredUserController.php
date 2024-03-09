<?php declare(strict_types=1);
/**
 * Blacknova Traders, a Free & Opensource (FOSS), web-based 4X space/strategy game.
 *
 * @copyright 2024 Simon Dann, Ron Harwood and the BNT development team
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

        /** @var User $user */
        $user = User::create([
            'name' => $request->get('character_name'),
            'locale' => $request->get('locale', config('app.locale')),
            'turns' => config('game.start_turns'),

            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        // Create Ship
        // TODO: Create Ship Enum and have this largely placed there
        $ship = new Ship();
        $ship->owner_id = $user->id;
        $ship->name = $request->get('ship_name');
        $ship->ship_destroyed = false;
        $ship->armor_pts = config('game.start_armor');
        $ship->ship_energy = config('game.start_energy');
        $ship->ship_fighters = config('game.start_fighters');
        $ship->on_planet = false;

        $ship->dev_warpedit = config('game.start_editors');
        $ship->dev_genesis = config('game.start_genesis');
        $ship->dev_beacon = config('game.start_beacon');
        $ship->dev_emerwarp = config('game.start_emerwarp');
        $ship->dev_escapepod = config('game.start_escape_pod');
        $ship->dev_fuelscoop = config('game.start_scoop');
        $ship->dev_lssd = config('game.start_lssd');
        $ship->dev_minedeflector = config('game.start_minedeflectors');

        $ship->trade_colonists = true;
        $ship->trade_fighters = false;
        $ship->trade_torps = false;
        $ship->trade_energy = true;
        $ship->cleared_defenses = null;
        $ship->system_id = 1;
        $ship->save();

        $user->ship()->associate($ship);

        $user->ship_id = $ship->id;
        $user->save();

        // TODO listen to Login event and update 'last_login' => Carbon::now(),

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
