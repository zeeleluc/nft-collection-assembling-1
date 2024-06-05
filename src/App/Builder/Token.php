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

    public function debug(bool $isDebug = true): self
    {
        $this->debug = $isDebug;

        return $this;
    }

    public function build(): self
    {
        if (fifty_fifty_chance()) {
            $tokenTrait = new TokenTrait('Background');
        } else {
            $colorNames = colors_resolver();
            shuffle($colorNames);
            $tokenTrait = new TokenTrait('Background::' . $colorNames[0], false);
        }
        $this->logic->addTrait($tokenTrait);
        $this->image->addTrait($tokenTrait);
        $this->metadata->addTrait($tokenTrait);

        foreach ($this->logic->traitOrder() as $trait) {
            $tokenTrait = new TokenTrait($trait);

            if ($this->logic->isTraitMandatory($tokenTrait)) {
                if ($this->logic->canHaveTrait($tokenTrait)) { // it is always mandatory "if"
                    $this->logic->addTrait($tokenTrait);
                    $this->image->addTrait($tokenTrait);
                    $this->metadata->addTrait($tokenTrait);
                }
            } else {
                if (in_array($tokenTrait->name, ['Front Layer'])) {
                    $do = rarity_chance(25);
                } elseif (in_array($tokenTrait->name, ['Special', 'Plebs Heads'])) {
                    $do = rarity_chance(10);
                } else {
                    $do = fifty_fifty_chance();
                }

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
        $this->image->render($this->debug);

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
