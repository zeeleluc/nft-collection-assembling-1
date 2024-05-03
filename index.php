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

    $session = 'test';

    for ($i = 0; $i < 100; $i++) {
        (new \App\Builder\Token($session, $i))
            ->build()
            ->renderImage()
            ->renderMetadata();

        echo $i . PHP_EOL;
    }
}
