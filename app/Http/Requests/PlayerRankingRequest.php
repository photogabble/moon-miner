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

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\PlayerRankingResource;

class PlayerRankingRequest extends FormRequest
{

    private array $availablePlayerSorts = ['score', 'turns', 'login', 'good', 'evil', 'efficiency'];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            [
                'sort_players_by' => ['sometimes', new In($this->availablePlayerSorts)],
                'sort_players_direction' => ['sometimes', new In(['asc', 'desc'])],
            ]
        ];
    }

    public function fetch(): array
    {
        $efficiencyPartial = DB::connection()->getDriverName() === 'sqlite'
            ? DB::raw('IIF (turns_used < 150, 0, ROUND(users.score/turns_used)) as efficiency')
            : DB::raw('IF (turns_used < 150, 0, ROUND(users.score/turns_used)) as efficiency');

        $playerRankingQuery = User::query()
            //->where('turns_used', '>', 0)
            ->select([
                'id',
                'name',
                'type',
                'turns_used',
                'score',
                'last_login',
                'rating',
                'rank',
                $efficiencyPartial
            ]);

        $sortPlayerDirection = strtoupper($this->get('sort_players_direction', 'DESC'));

        switch($this->get('sort_players_by')) {
            case 'turns':
                $playerRankingQuery
                    ->orderBy('turns_used', $sortPlayerDirection)
                    ->orderBy('name', 'ASC');
                break;
            case 'login':
                $playerRankingQuery
                    ->orderBy('last_login', $sortPlayerDirection)
                    ->orderBy('name', 'ASC');
                break;
            case 'good':
                $playerRankingQuery
                    ->orderBy('rating', 'DESC')
                    ->orderBy('name', 'ASC');
                break;
            case 'evil':
                $playerRankingQuery
                    ->orderBy('rating', 'ASC')
                    ->orderBy('name', 'ASC');
                break;
            case 'efficiency':
                $playerRankingQuery
                    ->orderBy('efficiency', $sortPlayerDirection);
                break;
            default:
                $playerRankingQuery
                    ->orderBy('score', $sortPlayerDirection)
                    ->orderBy('name', 'ASC');
        }

        return [
            'sorts' => $this->availablePlayerSorts,
            'sorting_by' => $this->get('sort_players_by', 'score'),
            'sorting_direction' => $sortPlayerDirection, //Sorting::from($this->request->get('sort_players_direction', 'desc')),
            'ranking' => PlayerRankingResource::collection($playerRankingQuery->paginate(25, ['*'], 'player_page')->withQueryString()),
        ];
    }
}
