<?php

namespace App;

use App\Builder\TokenTrait;
use App\Traits\TokenTraitTrait;

class Logic
{

    use TokenTraitTrait;

    public function __construct()
    {
    }

    public function canHaveTrait(TokenTrait $tokenTrait): bool
    {
        return true;
    }

    public function traitOrder(): array
    {
        return [
            'Background',
            'Back Props',
            'Body',
            'Clothes',
            'Eyes',
            'Hair',
            'Hands',
            'Hats',
            'Mouth',
            'Accessories',
        ];
    }

    public function mandatoryTraits(): array
    {
        return [
            'Background',
            'Body',
            'Mouth',
            'Eyes',
        ];
    }

    public function isTraitMandatory(TokenTrait $tokenTrait): bool
    {
        return in_array($tokenTrait->name, $this->mandatoryTraits());
    }
}
