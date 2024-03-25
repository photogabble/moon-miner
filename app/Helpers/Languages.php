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

namespace App\Helpers;

use SplFileInfo;
use DirectoryIterator;

class Languages
{
    /**
     * Get a list of supported languages
     * @return array
     */
    public static function listAvailable(): array
    {
        return array_reduce(self::listAvailableKeys(), function (array $list, string $key){
            $list[$key] = [
                'name' => __('regional.local_lang_name', [], $key),
                'flag' => __('regional.local_lang_flag', [], $key),
            ];
            return $list;
        }, []);
    }

    public static function listAvailableKeys(): array
    {
        $dir = new DirectoryIterator(lang_path());
        $list = [];
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $list[] = $fileInfo->getFilename();
            }
        }
        return $list;
    }
}
