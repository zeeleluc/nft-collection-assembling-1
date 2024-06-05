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

if (!function_exists('truncate_folder')) {
    function truncate_folder(string $folderPath, string $folderName): void
    {
        $folderPath = rtrim($folderPath, '/') . '/';
        if (!is_dir($folderPath . $folderName)) {
            return;
        }
        $contents = scandir($folderPath . $folderName);
        $contents = array_diff($contents, array('.', '..'));
        foreach ($contents as $item) {
            $itemPath = $folderPath . $folderName . '/' . $item;
            if (is_dir($itemPath)) {
                truncate_folder($folderPath . $folderName . '/', $item);
            } else {
                unlink($itemPath);
            }
        }
        rmdir($folderPath . $folderName);
    }
}

if (!function_exists('colors_resolver')) {
    function colors_resolver(string $forColorName = ''): string|array
    {
        $colors = [
            'black' => '#000000',
            'gray' => '#808080',
            'cloud' => '#c5c600',
            'orange' => '#f28500',
            'wine' => '#722F37',
            'merlot red' => '#b11226',
            'blue' => '#3944bc',
            'azure' => '#1520a6',
            'Black' => '#000000',
            'Gray' => '#808080',
            'Pee' => '#c5c600',
            'Orange' => '#f28500',
            'Wine' => '#722F37',
            'Blood' => '#b11226',
            'Blue' => '#3944bc',
            'Azure' => '#1520a6',
        ];

        if ($forColorName) {
            return $colors[$forColorName];
        }

        return array_keys($colors);
    }
}
