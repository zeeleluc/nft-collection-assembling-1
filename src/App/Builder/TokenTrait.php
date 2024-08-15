<?php

namespace App\Builder;

use App\Logic;

class TokenTrait
{

    public TokenTraitProperty $tokenTraitProperty;

    private Logic $logic;

    public function __construct(Logic $logic, public string $name, public bool $hasImage = true)
    {
        $this->logic = $logic;

        if ($hasImage) {
            $this->resolveProperty();
        } else {
            $traitChunks = explode('::', $this->name);
            if ($traitChunks && is_array($traitChunks) && isset($traitChunks[1])) {
                $this->name = $traitChunks[0];
                $this->tokenTraitProperty = new TokenTraitProperty($traitChunks[1]);
            }
        }
    }

    private function resolveProperty(): void
    {
        if ($this->logic->isGif && ($this->name === $this->logic->getTheGifProperty())) {
            $properties = glob(ROOT . '/properties/' . $this->name . '/*.GIF');
        } else {
            $properties = glob(ROOT . '/properties/' . $this->name . '/*.png');
        }

        if ($this->name === 'Body') {
            $properties = array_diff($properties, [
                // Coreum
                '/var/www/properties/Body/Book.GIF',
                '/var/www/properties/Body/Coin.GIF',
                '/var/www/properties/Body/Goldie Gary.GIF',
                '/var/www/properties/Body/Zombie Judge.GIF',
                '/var/www/properties/Body/Flash Drive Coreum.GIF',

                // Solana
//                '/var/www/properties/Body/Zombie 1 Judge.GIF',
//                '/var/www/properties/Body/Zombie 2.GIF',
//                '/var/www/properties/Body/Zombie 3 Solana Book.GIF',
//                '/var/www/properties/Body/Zombie 4 Solana Coin.GIF',
//                '/var/www/properties/Body/Solana.GIF',

                // XRPL
//                '/var/www/properties/Body/XRP Card.GIF',
//                '/var/www/properties/Body/XRP Flag.GIF',
//                '/var/www/properties/Body/XRP Emblem.GIF',
//                '/var/www/properties/Body/XRP Gold.GIF',
//                '/var/www/properties/Body/Ball.GIF',

                // Base
//                '/var/www/properties/Body/Base Coin.gif',
//                '/var/www/properties/Body/Gold Ledger.gif',
//                '/var/www/properties/Body/Golden Bell.gif',
//                '/var/www/properties/Body/Judge Hammer.gif',
//                '/var/www/properties/Body/Book.gif',
            ]);
        }

        shuffle($properties);

//        var_dump($properties);

        $this->tokenTraitProperty = new TokenTraitProperty(pathinfo($properties[0])['filename']);
    }

    public function setTokenTraitProperty(string $name): self
    {
        $this->tokenTraitProperty = new TokenTraitProperty(pathinfo($name)['filename']);

        return $this;
    }

    public function getTokenTraitProperty(): TokenTraitProperty
    {
        return $this->tokenTraitProperty;
    }
}
