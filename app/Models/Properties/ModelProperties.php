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

use JsonException;
use JsonSerializable;
use ReflectionObject;
use ReflectionProperty;

abstract class ModelProperties implements JsonSerializable
{
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public static function make(array $attributes = []): ModelProperties
    {
        return new static($attributes);
    }

    abstract public function fill(array $attributes = []): void;

    public function jsonSerialize(): mixed
    {
        $reflectionObject = (new ReflectionObject($this));
        $properties = $reflectionObject->getProperties(ReflectionProperty::IS_PUBLIC);
        $results = [];

        foreach ($properties as $property) {
            if ($property->isInitialized($this)) {
                $results[$property->getName()] = $property->getValue($this);
            }
        }

        $json = json_encode($results);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(json_last_error_msg());
        }

        return $json;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
