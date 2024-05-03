<?php

namespace App\Traits;

use App\Builder\TokenTrait;

trait TokenTraitTrait
{

    /**
     * @var array|\App\Builder\TokenTrait[]
     */
    public array $tokenTraits = [];

    public function addTrait(TokenTrait $tokenTrait): self
    {
        $this->tokenTraits[] = $tokenTrait;

        return $this;
    }
}
