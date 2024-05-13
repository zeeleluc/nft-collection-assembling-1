<?php

namespace App\Traits;

use App\Builder\TokenTrait;

trait TokenTraitTrait
{

    /**
     * @var array|TokenTrait[]
     */
    public array $tokenTraits = [];

    public function addTrait(TokenTrait $tokenTrait): self
    {
        $this->tokenTraits[] = $tokenTrait;

        return $this;
    }

    public function hasTrait(string $name): bool
    {
        foreach ($this->tokenTraits as $tokenTrait) {
            if ($tokenTrait->name === $name) {
                return true;
            }
        }

        return false;
    }

    public function hasTraitProperty(string $traitName, string $propertyName): bool
    {
        foreach ($this->tokenTraits as $tokenTrait) {
            if ($tokenTrait->name === $traitName) {
                if ($tokenTrait->tokenTraitProperty->name === $propertyName) {
                    return true;
                }
            }
        }

        return false;
    }
}
