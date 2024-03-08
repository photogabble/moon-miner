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
// File: app/Actions/Languages.php

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
        $dir = new DirectoryIterator(lang_path());

        $langList = [];
        /** @var SplFileInfo $fileInfo */
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $langList[$fileInfo->getFilename()] = [
                    'name' => __('regional.local_lang_name', [], $fileInfo->getFilename()),
                    'flag' => __('regional.local_lang_flag', [], $fileInfo->getFilename()),
                ];
            }
        }

        return $langList;
    }
}