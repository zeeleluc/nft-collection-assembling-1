<?php

namespace App\Builder;

use App\Creator\Image;
use App\Creator\Metadata;
use App\Logic;

class Token
{
    private Image $image;

    private Metadata $metadata;

    private Logic $logic;

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

    public function __construct(
        private readonly string $session,
        private readonly int $id
    ) {
        $this->image = new Image($session, $id);
        $this->metadata = new Metadata();
        $this->logic = new Logic();
    }

    public function renderImage(): self
    {
        $this->image->render();

        return $this;
    }

    public function renderMetadata(): self
    {
        return $this;
    }
}
