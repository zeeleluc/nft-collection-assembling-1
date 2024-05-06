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
        $traitCantHaveOtherTrait = [
            'Plebs Heads' => [
                'Eyes',
                'Mouth',
            ],
        ];

        foreach ($traitCantHaveOtherTrait as $traitName => $otherTraitName) {
            if ($this->hasTrait($traitName)) {
                return ! in_array($tokenTrait->name, $otherTraitName);
            }
        }

        return true;
    }

    public function traitOrder(): array
    {
        return [
            'Back Props',
            'Body',
            'Plebs Heads',
            'Clothes',
            'Eyes',
            'Hair',
            'Hands',
            'Hats',
            'Mouth',
            'Accessories',

            // special
//            'Plebs_unique_pieces',
//            'Unique_Mis.',
        ];
    }

    public function mandatoryTraits(): array
    {
        return [
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
