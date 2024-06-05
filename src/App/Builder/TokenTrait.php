<?php

namespace App\Builder;

class TokenTrait
{

    public TokenTraitProperty $tokenTraitProperty;

    public function __construct(public string $name, public bool $hasImage = true)
    {
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
        $properties = glob(ROOT . '/properties/' . $this->name . '/*.png');
        shuffle($properties);

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
