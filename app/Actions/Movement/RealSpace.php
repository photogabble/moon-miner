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

namespace App\Actions\Movement;

// Refactor:
// Create new PlotRealSpaceCourse Encounter
// Players can only plot courses to systems their NavCom knows about,
// currently that is only systems they have visited. I plan in the future
// to add the ability to buy maps that unlock one or more systems.
//
// Next to refactor -> galaxy.php


use Exception;
use App\Models\Ship;
use App\Helpers\Move;
use App\Models\System;
use App\Models\MovementLog;
use App\Types\MovementMode;

class RealSpace
{
    public function __construct(private Ship $ship){}

    public function calculateMoveTo(System $system): array
    {
        if ($system->id === $this->ship->system_id) throw new Exception('You are already within the target system');

        $distance = $this->ship->system
            ->toCartesian()
            ->distanceTo($system->toCartesian());

        $shipSpeed = pow(config('game.level_factor'), $this->ship->engines);
        $turns = max(1, (int)round($distance / $shipSpeed));

        // Used by frontend to output:
        // With your engines, it will take X turns to complete the journey.
        // You would gather Y units of energy.
        return [
            'turns' => $turns,
            'distance' => $distance,
            'energyScooped' => Move::calcFuelScooped($this->ship, $distance, $turns),
        ];
    }

    public function moveTo(System $system): MovementLog
    {
        $data = $this->calculateMoveTo($system);

        if ($this->ship->owner->turns < $data['turns']) {
            // Exception should cause the frontend to output:
            // With your engines, it will take X turns to complete the journey.
            // You do not have enough turns left, and cannot embark on this journey.
            throw new Exception(__('rsmove.l_rs_noturns')); // TODO: Player Exception, these are more notices and warnings for players than error exceptions and should be treated differently!
        }

        return $this->ship->travelTo(
            $system->id,
            MovementMode::RealSpace,
            $data['turns'],
            $data['energyScooped']
        );
    }
}
