<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2024 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: App/Models/GameConfig.php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $section
 * @property string $name
 * @property string $category
 * @property string $type
 * @property string $value
 */
class GameConfig extends Model
{
    protected $table = 'game_config';

    protected $fillable = ['key', 'type', 'value'];

    public $timestamps = false;

    public static function findByKey(string $key): mixed
    {
        /** @var GameConfig $found */
        if ($found = GameConfig::query()
            ->where('key', $key)
            ->first()) {

            /** @var mixed $value */
            $value = $found->value;
            settype($value, $found->type);
            return $value;
        }

        return null;
    }
}
