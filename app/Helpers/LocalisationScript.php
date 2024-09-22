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

use Exception;
use Stringable;


/**
 * Localisation string Script output, based upon that used by Tightenco\Ziggy
 */
class LocalisationScript implements Stringable
{
    protected string $trans;

    public function __construct(array $trans)
    {
        $this->trans = json_encode($trans);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Unable to encode translation strings to JSON: " . json_last_error_msg());
        }
    }

    public function __toString(): string
    {
        return <<<JS
<script type="text/javascript">
    const Translations = {$this->trans};
</script>
JS;
    }
}
