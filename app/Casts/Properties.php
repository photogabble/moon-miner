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

namespace App\Casts;

use App\Models\Properties\ModelProperties;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class Properties implements CastsAttributes
{
    protected string $castsTo;

    protected string $columnName = 'properties';

    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return ModelProperties
     */
    public function get($model, string $key, $value, array $attributes)
    {

        $data = json_decode($value, true);
        $properties = (json_last_error() === JSON_ERROR_NONE)
            ? $data
            : [];

        return new $this->castsTo($properties);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param ModelProperties $value
     * @param array $attributes
     * @return string
     * @throws \JsonException
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value instanceof $this->castsTo) {
            throw new InvalidArgumentException("The given value is not an $this->castsTo instance.");
        }

        return $value->jsonSerialize();
    }
}
