<?php

namespace App\Creator;

use App\Traits\TokenTraitTrait;

class Metadata
{
    use TokenTraitTrait;

    private string $url;

    public function __construct(
        private readonly string $session,
        private readonly int $id
    ) {
        $this->url = $this->resolvePath();
    }

    public function render(): void
    {
        $json = json_encode($this->createMeta());

        file_put_contents($this->url, $json);
    }

    public function getUniqueIdentifier(): string
    {
        $string = '';

        foreach ($this->tokenTraits as $tokenTrait) {
            $string .= $tokenTrait->name . $tokenTrait->tokenTraitProperty->name;
        }

        return md5($string);
    }

    private function createMeta(): array
    {
        $metadata = [];
        $metadata['name'] = 'WeepingPleb #' . $this->id;
        $metadata['description'] = '';
        $metadata['image'] = 'ipfs://CID-PLACEHOLDER/' . $this->id . '.png';
        foreach ($this->tokenTraits as $tokenTrait) {
            $metadata['attributes'][] = [
                'trait_type' => $tokenTrait->name,
                'value' => $tokenTrait->tokenTraitProperty->name,
            ];
        }

        return $metadata;
    }

    private function resolvePath(): string
    {
        $defaultPath = ROOT . '/generated/session';

        if (!file_exists($defaultPath . '/' . $this->session . '/collection/metadata')) {
            mkdir($defaultPath . '/' . $this->session . '/collection/metadata');
        }

        return $defaultPath . '/' . $this->session . '/collection/metadata/' . $this->id . '.json';
    }
}
