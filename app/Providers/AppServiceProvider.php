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

namespace App\Providers;

use App\Generators\Galaxy;
use App\Helpers\LocalisationScript;
use Illuminate\Support\Facades\File;
use App\Helpers\PerlinNoiseGenerator;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\View\Compilers\BladeCompiler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Galaxy::class, function (){
            return new Galaxy(
                new PerlinNoiseGenerator(2.0,6.0, 3),
                setting('game.sector_max')
            );
        });

        if ($this->app->resolved('blade.compiler')) {
            $this->registerDirective($this->app['blade.compiler']);
        } else {
            $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
                $this->registerDirective($bladeCompiler);
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }

    protected function registerDirective(BladeCompiler $bladeCompiler)
    {
        $bladeCompiler->directive('localisation', function () {
            $translation = $this->translations(app()->getLocale());
            $output = new LocalisationScript($translation);

            return (string) $output;
        });
    }

    protected function translations(string $locale): array
    {
        $translationFiles = File::files(base_path("lang/{$locale}"));

        return collect($translationFiles)
            ->map(function (SplFileInfo $file) {
                $ext = $file->getExtension();
                if ($ext === 'json') {
                    $data = json_decode(File::get($file->getPathname()));
                } else if ($ext === 'php') {
                    $data = require($file);
                } else {
                    $data = [];
                }

                return [$file->getFilenameWithoutExtension() => $data];
            })
            ->collapse()
            ->toArray();
    }
}
