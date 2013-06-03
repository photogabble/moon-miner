<?php
class BntRand
{
    static function sslRand ($min = 0, $max = 0x7FFFFFFF)
    {
        if (!function_exists ('opensslRandom_pseudo_bytes'))
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
                $feed = opensslRandom_pseudo_bytes ($bytes);
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
        if ($fp !== FALSE)
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
