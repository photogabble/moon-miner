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

namespace App\Console\Commands;

use Throwable;
use App\Helpers\Languages;
use App\Types\InstallConfig;
use Illuminate\Console\Command;
use App\Helpers\ExecutionTimer;
use Illuminate\Support\Facades\Log;

class BigBang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:big-bang {--use-defaults} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Line Installer';

    private array $stages = [

    ];

    public function __construct(private InstallConfig $installConfig)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $lang = $this->choice('Please select your language', array_map(function ($item) {
            return $item['name'];
        }, Languages::listAvailable()));

        app()->setLocale($lang);

        $this->line(__('create_universe.l_cu_welcome'));
        $this->line(__('create_universe.l_cu_allow_create'));

        if ($this->configureInstall() !== 0) return 1;

        // Fresh Migration
        if (!$this->option('force')) {
            if (!$this->confirm(__('create_universe.l_cu_table_drop_warn'))) return 1;
        }
        $this->call('migrate:fresh');

        // TODO: persist InstallConfig values so they can be displayed

        $this->components->info('Running Install Stages');

        $logger = Log::channel('install');

        foreach($this->stages as $stage) {
            try {
                $this->components->task($stage, fn() => (new $stage(new ExecutionTimer, $logger))->execute($this->output, $this->installConfig));
            } catch (Throwable $exception) {
                $this->error($exception->getMessage());
                $this->call('migrate:reset');
                return 1;
            }
        }

        $this->line(__('create_universe.l_cu_congrats_success'));

        return 0;
    }

    private function configureInstall(): int
    {
        // TODO: dont ask questions if $this->option('use-defaults') I think the InstallConfig class
        //       will need refactoring to contain default values.
        // if ($this->option('use-defaults')) return 0;

        // Ports
        $this->line(__('create_universe.l_cu_base_n_planets'));

        // Default values
        $special = 1;
        $ore = 15;
        $organics = 10;
        $goods = 15;
        $energy = 10;

        $this->installConfig->initialCommoditiesSellPercentage = 100;
        $this->installConfig->initialCommoditiesBuyPercentage = 100;
        $this->installConfig->federationSectors = 5;
        $this->installConfig->loops = 2;
        $planets = 10;

        while (true) {
            $empty = 100;

            $special = $this->askBetween(__('create_universe.l_cu_percent_special'), 0, 100, $special);
            $empty -= $special;

            $ore = $this->askBetween(__('create_universe.l_cu_percent_ore'), 0, $empty, $ore);
            $empty -= $ore;

            $organics = $this->askBetween(__('create_universe.l_cu_percent_organics'), 0, $empty, $organics);
            $empty -= $organics;

            $goods = $this->askBetween(__('create_universe.l_cu_percent_goods'), 0, $empty, $goods);
            $empty -= $goods;

            $energy = $this->askBetween(__('create_universe.l_cu_percent_energy'), 0, $empty, $energy);
            $empty -= $energy;

            $this->line(__('create_universe.l_cu_percent_empty', ['empty' => $empty]));

            // Commodities

            $this->installConfig->initialCommoditiesSellPercentage = $this->askBetween(__('create_universe.l_cu_init_comm_sell'), 0, 100, $this->installConfig->initialCommoditiesSellPercentage);
            $this->installConfig->initialCommoditiesBuyPercentage = $this->askBetween(__('create_universe.l_cu_init_comm_buy'), 0, 100, $this->installConfig->initialCommoditiesBuyPercentage);

            // Sector & link setup

            $this->line(__('create_universe.l_cu_sector_n_link'));

            $this->installConfig->maxSectors = $this->askBetween(__('create_universe.l_cu_sector_total'), 0, 10000, config('game.sector_max'));

            $this->installConfig->federationSectors = $this->askBetween(__('create_universe.l_cu_fed_sectors'), 0, $this->installConfig->maxSectors, $this->installConfig->federationSectors); // TODO: no need for l_cu_fedsec_smaller with $this->maxSectors here
            $this->installConfig->loops = $this->askBetween(__('create_universe.l_cu_num_loops'), 0, 100, $this->installConfig->loops); // TODO: discover value purpose and update min/max
            $planets = $this->askBetween(__('create_universe.l_cu_percent_unowned'), 0, 100, $planets);

            // Unsure what $autorun does...

            // $this->line(__('create_universe.l_cu_autorun'));

            // Confirm Values

            $this->installConfig->specialPorts = (int)round($this->installConfig->maxSectors * $special / 100);
            $this->installConfig->orePorts = (int)round($this->installConfig->maxSectors * $ore / 100);
            $this->installConfig->organicPorts = (int)round($this->installConfig->maxSectors * $organics / 100);
            $this->installConfig->goodsPorts = (int)round($this->installConfig->maxSectors * $goods / 100);
            $this->installConfig->energyPorts = (int)round($this->installConfig->maxSectors * $energy / 100);
            $this->installConfig->unownedPlanets = (int)round($this->installConfig->maxSectors * $planets / 100);

            $this->installConfig->emptySectors = $this->installConfig->maxSectors - $this->installConfig->specialPorts - $this->installConfig->orePorts - $this->installConfig->organicPorts - $this->installConfig->goodsPorts - $this->installConfig->energyPorts;

            $this->table(['Setting', 'Value'], [
                [__('create_universe.l_cu_special_ports'), $this->installConfig->specialPorts],
                [__('create_universe.l_cu_ore_ports'), $this->installConfig->orePorts],
                [__('create_universe.l_cu_organics_ports'), $this->installConfig->organicPorts],
                [__('create_universe.l_cu_goods_ports'), $this->installConfig->goodsPorts],
                [__('create_universe.l_cu_energy_ports'), $this->installConfig->energyPorts],
                [__('create_universe.l_cu_init_comm_sell'), $this->installConfig->initialCommoditiesSellPercentage],
                [__('create_universe.l_cu_init_comm_buy'), $this->installConfig->initialCommoditiesBuyPercentage],
                [__('create_universe.l_cu_empty_sectors'), $this->installConfig->emptySectors],
                [__('create_universe.l_cu_fed_sectors'), $this->installConfig->federationSectors],
                [__('create_universe.l_cu_loops'), $this->installConfig->loops],
                [__('create_universe.l_cu_unowned_planets'), $this->installConfig->unownedPlanets],
            ]);

            if ($this->option('force')) return 0;

            if ($this->confirm(__('create_universe.l_cu_confirm_settings', ['max_sectors' => $this->installConfig->maxSectors,]))) {
                return 0;
            }
        }
    }

    private function askBetween(string $ask, int $min, int $max, int $default): int
    {
        while(true) {
            $value = $this->ask("$ask [$min-$max]", $default);
            if ($value >= $min && $value <= $max) return (int)$value;
            $this->line("<error>[!]</error> Invalid input, please input a number between $min and $max");
        }
    }
}
