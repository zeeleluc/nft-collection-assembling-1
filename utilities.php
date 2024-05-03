<?php

if (!function_exists('is_cli')) {
    function is_cli(): bool
    {
        if ( defined('STDIN') ) {
            return true;
        }
        if ( php_sapi_name() === 'cli' ) {
            return true;
        }
        if ( array_key_exists('SHELL', $_ENV) ) {
            return true;
        }
        if ( empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
            return true;
        }
        if ( !array_key_exists('REQUEST_METHOD', $_SERVER) ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('fifty_fifty_chance')) {
    function fifty_fifty_chance(): bool
    {
        return rarity_chance(1);
    }
}

if (!function_exists('rarity_chance')) {
    function rarity_chance(int $rarity): bool
    {
        $array = range(0, $rarity);
        shuffle($array);
        return $array[0] == 0;
    }
}