<?php

use App\Models\GameConfig;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting(string $key, $default = null) {
        $cacheKey = "game-setting.$key";
        if (Cache::has($cacheKey)) return Cache::get($cacheKey);

        if (!$value = GameConfig::findByKey($key)) {
            $value = config($key, $default);
        }

        Cache::forever($cacheKey, $value);
        return $value;
    }
}
