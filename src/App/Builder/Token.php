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

    public function build(): self
    {
        foreach ($this->logic->traitOrder() as $trait) {
            $tokenTrait = new TokenTrait($trait);

            if ($this->logic->isTraitMandatory($tokenTrait)) {
                $this->logic->addTrait($tokenTrait);
                $this->image->addTrait($tokenTrait);
                $this->metadata->addTrait($tokenTrait);
            } else {
                if ($this->logic->canHaveTrait($tokenTrait)) {
                    if (fifty_fifty_chance()) {
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
        $this->image->render();

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
