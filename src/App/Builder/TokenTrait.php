<?php

namespace App\Builder;

class TokenTrait
{

    public TokenTraitProperty $tokenTraitProperty;

    public function __construct(public readonly string $name)
    {
        $this->resolveProperty();
    }

    private function resolveProperty()
    {
        $properties = glob(ROOT . '/properties/' . $this->name . '/*.png');
        shuffle($properties);

        $this->tokenTraitProperty = new TokenTraitProperty(pathinfo($properties[0])['filename']);
    }

    public function getTokenTraitProperty(): TokenTraitProperty
    {
        return $this->tokenTraitProperty;
    }
}
