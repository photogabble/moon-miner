<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
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
// File: classes/BntRand.php
//
// Function for importing values from an INI file into the database.

if (strpos ($_SERVER['PHP_SELF'], 'BntRand.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}


class BntRand
{
    static function sslRand ($min = 0, $max = 0x7FFFFFFF)
    {
        if (!function_exists ('openssl_random_pseudo_bytes'))
        {
            return false; // Open ssl is not available
        }
        else
        {
            $range = $max - $min;
            if ($range < 1 || $range > 0x7FFFFFFF)
            {
                return false; // Either less than random (min = max or less), or larger than we can handle
            }
            $log = log ($range, 2);
            $bytes = (int) ($log / 8) + 1; // Length in bytes
            $bits = (int) $log + 1; // Length in bits
            $filter = (int) (1 << $bits) - 1; // Set all lower bits to 1
            do
            {
                $feed = openssl_random_pseudo_bytes ($bytes);
                if ($feed === false || strlen ($feed) != $bytes)
                {
                    return false; // Unable to generate sufficient bytes
                }
                else
                {
                    $rnd = hexdec (bin2hex ($feed));
                    $rnd = $rnd & $filter; // Discard irrelevant bits
                }
            }
            while ($rnd >= $range);
            return $min + $rnd;
        }
    }

    static function mcryptRand ($min = 0, $max = 0x7FFFFFFF)
    {
        if (!function_exists ('mcrypt_create_iv'))
        {
            return false; // mcrypt is not available
        }
        else
        {
            $range = $max - $min;
            if ($range < 1 || $range > 0x7FFFFFFF)
            {
                return false; // Either less than random (min = max or less), or larger than we can handle
            }
            $log = log ($range, 2);
            $bytes = (int) ($log / 8) + 1; // Length in bytes
            $bits = (int) $log + 1; // Length in bits
            $filter = (int) (1 << $bits) - 1; // Set all lower bits to 1
            do
            {
                $feed = mcrypt_create_iv ($bytes);
                if ($feed === false || strlen ($feed) != $bytes)
                {
                    return false; // Unable to generate sufficient bytes
                }
                else
                {
                    $rnd = hexdec (bin2hex ($feed));
                    $rnd = $rnd & $filter; // Discard irrelevant bits
                }
            }
            while ($rnd >= $range);
            return $min + $rnd;
        }
    }

    static function uRand ($min = 0, $max = 0x7FFFFFFF)
    {
        $bits = '';
        $range = $max - $min;
        $bytes = ceil ($range / 256);
        $fp = @fopen ('/dev/urandom', 'rb');
        if ($fp !== false)
        {
            $bits .= @fread ($fp, $bytes);
            @fclose ($fp);
        }
        $bitlength = strlen ($bits);
        for ($i = 0; $i < $bitlength; $i++)
        {
            $int =  1 + (ord ($bits[$i]) % (($max - $min) + 1));
        }
        return $int;
    }

    static function betterRand ($min = 0, $max = 0x7FFFFFFF)
    {
        $output = BntRand::sslRand ($min, $max);
        if ($output !== false)
        {
            return $output;
        }

        $output = BntRand::mcryptRand ($min, $max);
        if ($output !== false)
        {
            return $output;
        }

        return mt_rand ($min, $max);
    }
}
?>
