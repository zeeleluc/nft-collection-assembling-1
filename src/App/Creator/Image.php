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
    public function render(bool $debug = false)
    {
        foreach ($this->tokenTraits as $tokenTrait) {
            $propertyIdentifier = $tokenTrait->name . ': ' . $tokenTrait->tokenTraitProperty->name;
            if ($tokenTrait->hasImage) {
                $layer = new \Imagick($this->resolvePropertyPath($tokenTrait));
                $this->imagick->setImageColorspace($layer->getImageColorspace());
                $this->imagick->compositeImage($layer, $layer->getImageCompose(), 0, 0);

                $this->imagick->compositeImage($layer, \Imagick::COMPOSITE_ATOP, 0, 0);
            } else {
                if ($tokenTrait->name === 'Background') {
                    $this->imagick->newImage(1000, 1000, colors_resolver($tokenTrait->tokenTraitProperty->name));
                }
            }
        }

        if ($debug) {
            $y = 25;
            foreach ($this->tokenTraits as $tokenTrait) {
                $propertyIdentifier = $tokenTrait->name . ': ' . $tokenTrait->tokenTraitProperty->name;

                $draw = new \ImagickDraw();
                $draw->setTextAlignment(\Imagick::ALIGN_LEFT);
                $draw->setFontSize(18);
                $draw->setFillColor(new \ImagickPixel('#111111'));
                $draw->setTextUnderColor(new \ImagickPixel('#ffffff'));
                $draw->annotation(5, $y, $propertyIdentifier);
                $this->imagick->drawImage($draw);

                $y += 19;
            }
        }

        $this->imagick->writeImage($this->url);
    }
}
