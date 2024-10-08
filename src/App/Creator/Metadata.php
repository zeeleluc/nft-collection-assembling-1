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
            if ($tokenTrait->name !== 'Background' && $tokenTrait->name !== 'Face') {
                $string .= $tokenTrait->name . $tokenTrait->tokenTraitProperty->name;
            }
        }

        return md5($string);
    }

    private function createMeta(): array
    {
        $metadata = [];
        $metadata['name'] = 'Dickbutt #' . $this->id;
        $metadata['description'] = 'A Call to Arms... It’s time to rally the troops and take a stand! We’re all about a future where innovation thrives and crypto stays free from chokehold regulations. But under the watch of ‘you know who’, the SEC is putting a stranglehold on our decentralized dreams. Let this crosschain collection symbolize all that is defi & secure your spot in our loving community by owning a Gary G Genzler... the G is for Guzzler and don’t forget it!';
//        $metadata['edition'] = 1;
        $metadata['image'] = 'ipfs://CID-PLACEHOLDER/' . $this->id . '.gif';
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
