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
        $logic = $this->getPropertiesLogic();

        foreach ($logic as $logicProperties) {
            if ($logicProperties['cannot_have_trait'] === $tokenTrait->name) {
                if (is_null($logicProperties['cannot_have_property'])) {
                    if (is_null($logicProperties['having_property'])) {
                        if ($this->hasTrait($logicProperties['having_trait'])) {
                            return false;
                        }
                    }
                } else {
                    if ($logicProperties['cannot_have_property'] === $tokenTrait->tokenTraitProperty->name) {
                        if (is_null($logicProperties['having_property'])) {
                            if ($this->hasTrait($logicProperties['having_trait'])) {
                                return false;
                            }
                        } else {
                            if ($this->hasTraitProperty($logicProperties['having_trait'], $logicProperties['having_property'])) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    private function getPropertiesLogic(): array
    {
        $csvFile = file(ROOT . '/properties/logic.csv');
        $logic = [];
        foreach ($csvFile as $index => $line) {
            if ($index >= 1) {
                $data = str_getcsv($line);
                $logic[] =  [
                    'having_trait' => trim($data[0]),
                    'having_property' => trim($data[1]) ?: null,
                    'cannot_have_trait' => trim($data[2]),
                    'cannot_have_property' => trim($data[3]) ?: null,
                ];
            }
        }

        return $logic;
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
