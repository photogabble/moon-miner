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

namespace App\Models\Encounters;

use App\Models\Encounter;
use Parental\HasParent;
use Illuminate\View\View;
use App\Types\MovementMode;
use App\Actions\Encounters\Complete;

final class Navigation extends Encounter
{
    use HasParent;

    public function options(): array
    {
        return [
            'complete' => [
                'class' => Complete::class,
                'text' => 'OK',
            ]
        ];
    }

    public function getTitle(): ?string
    {
        if ($this->movement->mode === MovementMode::Warp){
            return 'Warp Jump Successful';
        }

        if ($this->movement->mode === MovementMode::RealSpace){
            return 'Real Space Move Complete';
        }

        if ($this->movement->mode === MovementMode::Towed){
            return 'You were towed out of system';
        }

        return 'Navigation Report';
    }

    public function render(): ?View
    {
        return null;
    }
}
