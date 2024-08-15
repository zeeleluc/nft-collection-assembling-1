<?php

namespace App\Creator;

use App\Builder\TokenTrait;
use App\Logic;
use App\Traits\TokenTraitTrait;

class Image
{

    use TokenTraitTrait;

    private \Imagick $imagick;

    private string $url;

    private string $propertyPath;

    private bool $isGif = false;

    private Logic $logic;

    private bool $baseGifLayerCreated = false;

    /**
     * @throws \ImagickException
     */
    public function __construct(
        private readonly string $session,
        private readonly int $id
    ) {
        $this->imagick = new \Imagick();
        $this->imagick->newImage(1000, 1000, '#ffffff');
        $this->imagick->setFormat($this->isGif ? 'gif' : 'png');

        $this->url = $this->resolvePath();
    }

    public function isGif(bool $isGif = true): self
    {
        $this->isGif = $isGif;

        return $this;
    }

    private function resolvePropertyPath(TokenTrait $tokenTrait): string
    {
        if ($this->isGif && ($tokenTrait->name === $this->logic->getTheGifProperty())) {
            return ROOT . '/properties/' . $tokenTrait->name . '/' . $tokenTrait->getTokenTraitProperty()->name . '.GIF';
        }

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

        if ($this->isGif) {
            return $defaultPath . '/' . $this->session . '/collection/images/' . $this->id . '.png';
        }

        return $defaultPath . '/' . $this->session . '/collection/images/' . $this->id . '.gif';
    }

    /**
     * @throws \ImagickException
     */
    public function render(Logic $logic, bool $debug = false)
    {
        $this->logic = $logic;
        $gifTmpPath = 'tmp/' . $this->session . '/' . $this->id . '/assembled_layers';

        foreach ($this->tokenTraits as $tokenTrait) {
            if ($tokenTrait->hasImage) {

                $image = $this->resolvePropertyPath($tokenTrait);
                $extension = pathinfo($image)['extension'];

                if ($extension === 'GIF') {
                    $this->extractGifLayer($image);
                    $this->baseGifLayerCreated = true;

                } else {
                    if ($this->baseGifLayerCreated) {
                        // attach other traits on each layer
                        $layers = glob($gifTmpPath . '/*.png');
                        foreach ($layers as $tmpFilePath) {
                            $gifLayer = new \Imagick($tmpFilePath);
                            $layer = new \Imagick($this->resolvePropertyPath($tokenTrait));
                            $gifLayer->setImageColorspace($layer->getImageColorspace());
                            $gifLayer->compositeImage($layer, $layer->getImageCompose(), 0, 0);
                            $gifLayer->compositeImage($layer, \Imagick::COMPOSITE_ATOP, 0, 0);
                            $gifLayer->writeImage($tmpFilePath);
                        }
                    }

                    // regular png
                    $layer = new \Imagick($this->resolvePropertyPath($tokenTrait));
                    $this->imagick->setImageColorspace($layer->getImageColorspace());
                    $this->imagick->compositeImage($layer, $layer->getImageCompose(), 0, 0);

                    $this->imagick->compositeImage($layer, \Imagick::COMPOSITE_ATOP, 0, 0);
                }
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

        if ($this->isGif) {
            $gif = new \Imagick();
            $gif->setFormat('gif');

            $layers = glob($gifTmpPath . '/*.png');
            foreach ($layers as $tmpFilePath) {
                $frame = new \Imagick($tmpFilePath);
                $frame->setImageFormat('png');
                $frame->setImageDelay(1); // Adjust the delay as needed
                $gif->addImage($frame);
                $gif->nextImage();
            }

            $gif = $gif->coalesceImages();
            $gif->optimizeImageLayers();

            $gif->writeImages($this->url, true);

            $gif->clear();
            $gif->destroy();

            truncate_folder('tmp/' . $this->session, $this->id);

        } else {
            $this->imagick->writeImage($this->url);
        }
    }

    private function extractGifLayer(string $gifPath): string
    {
        if (!file_exists('tmp/' . $this->session)) {
            mkdir('tmp/' . $this->session);
        }
        mkdir('tmp/' . $this->session . '/' . $this->id);
        mkdir('tmp/' . $this->session . '/' . $this->id . '/gif_layers');
        mkdir('tmp/' . $this->session . '/' . $this->id . '/assembled_layers');

        $tmpFolderGifLayers = 'tmp/' . $this->session . '/' . $this->id . '/gif_layers';
        $tmpAssembledGifLayers = 'tmp/' . $this->session . '/' . $this->id . '/assembled_layers';

        $gif = new \Imagick();
        $gif->readImage($gifPath);

        $layerStart = 1;
        foreach ($gif as $index => $frame) {
            $gif->setIteratorIndex($index);

            // fetch and save the gif layers
            $frame = $gif->getImage();
            $frame->setImageFormat('png');
            $outputFile = $tmpFolderGifLayers . '/' . $layerStart . '.png';
            $frame->writeImage($outputFile);

            // paste the body layers on top of the previous layers
            $withPreviousLayers = clone $this->imagick;
            $gifLayer = new \Imagick(realpath($outputFile));
            $withPreviousLayers->compositeImage($gifLayer, $gifLayer->getImageCompose(), 0, 0);
            $outputFile = $tmpAssembledGifLayers . '/' . $layerStart . '.png';
            $withPreviousLayers->writeImage($outputFile);

            $layerStart++;
        }

        $gif->clear();
        $gif->destroy();

        truncate_folder('tmp/' . $this->session . '/' . $this->id, 'gif_layers');

        return 'tmp/' . $this->session . '/' . $this->id;
    }
}
