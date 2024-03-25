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

namespace App\Models\Properties;

/**
 * @method static WarpGateProperties make(array $attributes = [])
 */
final class WarpGateProperties extends ModelProperties
{
    // WarpGate with a null destination is a disabled gate. If a player has control of a system they
    // can edit the gate to point at a nearby system within range. Range will be determined by a
    // future upgrades system but for now it can be fixed to a certain number of ly same as warp
    // edits are in the classic game.
    public ?int $destination_system_id;

    public function fill(array $attributes = []): void
    {
        $this->destination_system_id = $attributes['destination_system_id'] ?? null;
    }
}
