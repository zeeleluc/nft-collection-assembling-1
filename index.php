<?php
include_once 'preload.php';
include_once 'autoloader.php';
include_once 'utilities.php';

if (!is_cli()) {
    exit('This is a terminal only application.');
}

if (!$argv[1]) {
    exit('Missing command');
}

$command = $argv[1];

if (in_array('create', $argv)) {
    (new \App\Builder\Token(1))
        ->renderImage()
        ->renderMetadata();
}
