<?php

namespace App\Builder;

class TokenTrait
{

    private TokenTraitProperty $tokenTraitProperty;

    public function __construct(public readonly string $name)
    {
        $this->resolveProperty();
    }

    private function resolveProperty()
    {
        $properties = glob(ROOT . '/properties/' . $this->name . '/*.png');
        shuffle($properties);

        $this->tokenTraitProperty = new TokenTraitProperty($properties[0]);
    }

    public function getTokenTraitProperty(): TokenTraitProperty
    {
        return $this->tokenTraitProperty;
    }
}
