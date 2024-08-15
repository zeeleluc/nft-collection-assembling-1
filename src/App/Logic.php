<?php

namespace App;

use App\Builder\TokenTrait;
use App\Traits\TokenTraitTrait;

class Logic
{

    use TokenTraitTrait;

    public bool $isGif = false;

    public function __construct()
    {
    }

    public function isGif(bool $isGif = true): self
    {
        $this->isGif = $isGif;

        return $this;
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
                    } else {
                        if ($this->hasTraitProperty($logicProperties['having_trait'], $logicProperties['having_property'])) {
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

    public function getTheGifProperty(): string
    {
        return 'Body';
    }

    public function traitOrder(): array
    {
        return [
            'Body',
            'Face',
            'Clothes',
            'Earring',
            'Head',
        ];
    }

    public function mandatoryTraits(): array
    {
        return [
            'Background',
            'Body',
            'Face',
        ];
    }

    public function isTraitMandatory(TokenTrait $tokenTrait): bool
    {
        return in_array($tokenTrait->name, $this->mandatoryTraits());
    }
}
