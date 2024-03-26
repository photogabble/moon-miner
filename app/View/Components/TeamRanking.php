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

namespace App\View\Components;

use App\Models\User;
use App\Models\Team;
use App\Types\Sorting;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;

class TeamRanking extends Component
{
    public function __construct(private readonly Request $request) {
        //
    }

    public function render(): View
    {
        $availableTeamSorts = ['score', 'members', 'login', 'good', 'evil', 'efficiency'];

        $this->request->validate([
            'sort_team_by' => ['sometimes', new In($availableTeamSorts)],
            'sort_team_direction' => ['sometimes', new In(['asc', 'desc'])],
        ]);

        $efficiencyPartial = DB::connection()->getDriverName() === 'sqlite'
            ? DB::raw('IIF (SUM(users.turns_used) < 150, 0, ROUND(SUM(users.score)/SUM(users.turns_used))) as efficiency')
            : DB::raw('IF (SUM(users.turns_used) < 150, 0, ROUND(SUM(users.score)/SUM(users.turns_used))) as efficiency');

        $teamRankingQuery = Team::query()
            ->leftJoin('users', 'users.team_id', '=', 'teams.id')
            ->groupBy('teams.id')
            ->select(
                'teams.name',
                DB::raw('COUNT(users.id) as player_count'),
                DB::raw('SUM(users.turns_used) as turns_used_sum'),
                DB::raw('SUM(users.score) as score_sum'),
                DB::raw('SUM(users.rating) as rating_sum'),
                $efficiencyPartial
            );

        $sortTeamDirection = strtoupper($this->request->get('sort_teams_direction', 'DESC'));

        switch($this->request->get('sort_teams_by')) {
            case 'turns':
                $teamRankingQuery
                    ->orderBy('turns_used_sum', $sortTeamDirection)
                    ->orderBy('name', 'ASC');
                break;
            case 'members':
                $teamRankingQuery
                    ->orderBy('player_count', $sortTeamDirection)
                    ->orderBy('name', 'ASC');
                break;
            case 'good':
                $teamRankingQuery
                    ->orderBy('rating_sum', 'DESC')
                    ->orderBy('name', 'ASC');
                break;
            case 'evil':
                $teamRankingQuery
                    ->orderBy('rating_sum', 'ASC')
                    ->orderBy('name', 'ASC');
                break;
            case 'efficiency':
                $teamRankingQuery
                    ->orderBy('efficiency', $sortTeamDirection);
                break;
            default:
                $teamRankingQuery
                    ->orderBy('score_sum', $sortTeamDirection)
                    ->orderBy('name', 'ASC');
        }

        return view('components.ranking.team', [
            'sortingBy' => $this->request->get('sort_teams_by', 'score'),
            'direction' => Sorting::from($this->request->get('sort_teams_direction', 'desc')),
            'teams' => $teamRankingQuery->paginate(25, ['*'], 'team_page')->withQueryString(),
        ]);
    }
}
