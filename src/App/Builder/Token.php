<?php

namespace App\Builder;

use App\Creator\Image;
use App\Creator\Metadata;
use App\Logic;
use ImagickException;

class Token
{
    private Image $image;

    private Metadata $metadata;

    private Logic $logic;

    private bool $debug = false;

    /**
     * @throws ImagickException
     */
    public function __construct(
        readonly private string $session,
        int $id
    ) {
        $this->image = new Image($session, $id);
        $this->metadata = new Metadata($session, $id);
        $this->logic = new Logic();
    }

    public function isGif(bool $isGif = true): self
    {
        $this->logic->isGif($isGif);
        $this->image->isGif($isGif);

        return $this;
    }

    public function debug(bool $isDebug = true): self
    {
        $this->debug = $isDebug;

        return $this;
    }

    public function setProperty(TokenTrait $tokenTrait): self
    {
        $this->logic->addTrait($tokenTrait);
        $this->image->addTrait($tokenTrait);
        $this->metadata->addTrait($tokenTrait);

        return $this;
    }

    public function build(int $id): self
    {
        $tokenTrait = new TokenTrait($this->logic, 'Background');
//        if (fifty_fifty_chance()) {
//            $tokenTrait = new TokenTrait($this->logic, 'Background');
//        } else {
//            $colorNames = colors_resolver();
//            shuffle($colorNames);
//            $tokenTrait = new TokenTrait($this->logic, 'Background::' . $colorNames[0], false);
//        }
        $this->logic->addTrait($tokenTrait);
        $this->image->addTrait($tokenTrait);
        $this->metadata->addTrait($tokenTrait);

        foreach ($this->logic->traitOrder() as $trait) {
            $tokenTrait = new TokenTrait($this->logic, $trait);
            if ($trait === 'Body') {

                // Coreum
                if ($id === 1378) {
                    $tokenTrait->setTokenTraitProperty('Book');
                } elseif ($id === 2846) {
                    $tokenTrait->setTokenTraitProperty('Coin');
                } elseif ($id === 4231) {
                    $tokenTrait->setTokenTraitProperty('Goldie Gary');
                } elseif ($id === 1927) {
                    $tokenTrait->setTokenTraitProperty('Zombie Judge');
                } elseif ($id === 3490) {
                    $tokenTrait->setTokenTraitProperty('Flash Drive Coreum');
                }

                // Solana
//                if ($id === 3427) {
//                    $tokenTrait->setTokenTraitProperty('Zombie 1 Judge');
//                } elseif ($id === 1985) {
//                    $tokenTrait->setTokenTraitProperty('Zombie 2');
//                } elseif ($id === 478) {
//                    $tokenTrait->setTokenTraitProperty('Zombie 3 Solana Book');
//                } elseif ($id === 2690) {
//                    $tokenTrait->setTokenTraitProperty('Zombie 4 Solana Coin');
//                } elseif ($id === 4122) {
//                    $tokenTrait->setTokenTraitProperty('Solana');
//                }

//                // XRPL
//                if ($id === 1789) {
//                    $tokenTrait->setTokenTraitProperty('Ball');
//                } elseif ($id === 1234) {
//                    $tokenTrait->setTokenTraitProperty('XRP Card');
//                } elseif ($id === 2876) {
//                    $tokenTrait->setTokenTraitProperty('XRP Emblem');
//                } elseif ($id === 3998) {
//                    $tokenTrait->setTokenTraitProperty('XRP Flag');
//                } elseif ($id === 4512) {
//                    $tokenTrait->setTokenTraitProperty('XRP Gold');
//                }

                // Base
//                if ($id === 3918) {
//                    $tokenTrait->setTokenTraitProperty('Base Coin');
//                } elseif ($id === 779) {
//                    $tokenTrait->setTokenTraitProperty('Gold Ledger');
//                } elseif ($id === 2522) {
//                    $tokenTrait->setTokenTraitProperty('Golden Bell');
//                } elseif ($id === 3813) {
//                    $tokenTrait->setTokenTraitProperty('Judge Hammer');
//                } elseif ($id === 1252) {
//                    $tokenTrait->setTokenTraitProperty('Book');
//                }
            }

            if ($this->logic->isTraitMandatory($tokenTrait)) {
                if ($this->logic->canHaveTrait($tokenTrait)) { // it is always mandatory "if"
                    $this->logic->addTrait($tokenTrait);
                    $this->image->addTrait($tokenTrait);
                    $this->metadata->addTrait($tokenTrait);
                }
            } else {
                $do = fifty_fifty_chance();
                if ($do) {
                    if ($this->logic->canHaveTrait($tokenTrait)) {
                        $this->logic->addTrait($tokenTrait);
                        $this->image->addTrait($tokenTrait);
                        $this->metadata->addTrait($tokenTrait);
                    }
                }
            }
        }

        return $this;
    }

    public function validateUniqueness():? self
    {
        if (file_exists($this->resolveUniquenessPath())) {
            return null;
        }

        return $this;
    }

    public function captureUniqueness(): void
    {
        file_put_contents($this->resolveUniquenessPath(), '');
    }

    /**
     * @throws ImagickException
     */
    public function renderImage(): self
    {
        $this->image->render($this->logic, $this->debug);

        return $this;
    }

    public function renderMetadata(): self
    {
        $this->metadata->render();

        return $this;
    }

    private function resolveUniquenessPath(): string
    {
        $defaultPath = ROOT . '/generated/session';

        if (!file_exists($defaultPath . '/' . $this->session . '/collection/uniqueness')) {
            mkdir($defaultPath . '/' . $this->session . '/collection/uniqueness');
        }

        return $defaultPath . '/' . $this->session . '/collection/uniqueness/' . $this->metadata->getUniqueIdentifier();
    }
}
