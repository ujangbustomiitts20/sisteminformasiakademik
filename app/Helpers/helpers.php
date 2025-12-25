<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::getValue($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Get all settings as array
     *
     * @return array
     */
    function settings(): array
    {
        return Setting::getAll();
    }
}
