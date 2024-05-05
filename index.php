<?php
include_once 'preload.php';
include_once 'autoloader.php';
include_once 'utilities.php';

const SESSION = 'test';

if (!is_cli()) {
    exit('This is a terminal only application.');
}

if (!$argv[1]) {
    exit('Missing command');
}

$command = $argv[1];
$commandValue = $argv[2] ?? null;

if (in_array('clear', $argv)) {
    if ($commandValue) {
        truncate_folder(ROOT . '/generated/session/', $commandValue);
    } else {
        echo 'Session missing... ' . PHP_EOL;
    }
}

if (in_array('create', $argv)) {
    $id = $commandValue ?? 1;

    if ($id === 1) {
        truncate_folder(ROOT . '/generated/session/', SESSION);
    }

    for ($i = 1; $i < 7777; $i++) {

        $uniqueNFT = null;
        do {
            try {
                $uniqueNFT = (new \App\Builder\Token(SESSION, $i))
                    ->build()
                    ->validateUniqueness();

                if ($uniqueNFT) {
                    $uniqueNFT->renderImage()
                        ->renderMetadata()
                        ->captureUniqueness();
                    echo 'NFT# ' . $i . ' done.' . PHP_EOL;
                } else {
                    echo 'NFT# ' . $i . ' duplicate found, try again...' . PHP_EOL;
                }
            } catch (ImagickException $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        } while(is_null($uniqueNFT));
    }
}
