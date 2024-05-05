<?php

namespace App\Creator;

use App\Builder\TokenTrait;
use App\Traits\TokenTraitTrait;

class Image
{

    use TokenTraitTrait;

    private \Imagick $imagick;

    private string $url;

    private string $propertyPath;

    /**
     * @throws \ImagickException
     */
    public function __construct(
        private readonly string $session,
        private readonly int $id
    ) {
        $this->imagick = new \Imagick();
        $this->imagick->newImage(1000, 1000, '#ffffff');
        $this->imagick->setFormat("png");

        $this->url = $this->resolvePath();
    }

    private function resolvePropertyPath(TokenTrait $tokenTrait): string
    {
        return ROOT . '/properties/' . $tokenTrait->name . '/' . $tokenTrait->getTokenTraitProperty()->name . '.png';
    }

    private function resolvePath(): string
    {
        $defaultPath = ROOT . '/generated/session';

        if (!file_exists($defaultPath . '/' . $this->session)) {
            mkdir($defaultPath . '/' . $this->session);
            mkdir($defaultPath . '/' . $this->session . '/collection');
            mkdir($defaultPath . '/' . $this->session . '/collection/images');
        }

        return $defaultPath . '/' . $this->session . '/collection/images/' . $this->id . '.png';
    }

    /**
     * @throws \ImagickException
     */
    public function render()
    {
        foreach ($this->tokenTraits as $tokenTrait) {
            $layer = new \Imagick($this->resolvePropertyPath($tokenTrait));
            $this->imagick->setImageColorspace($layer->getImageColorspace());
            $this->imagick->compositeImage($layer, $layer->getImageCompose(), 0, 0);

            $this->imagick->compositeImage($layer, \Imagick::COMPOSITE_ATOP, 0, 0);
        }

        $this->imagick->writeImage($this->url);
    }
}
