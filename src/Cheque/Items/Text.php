<?php

namespace Djalone\KkmServerClasses\Cheque\Items;

class Text extends Item
{
    private int $fontSize = 0;
    private int $intensity = 0;

    public function __construct(private readonly string $text)
    {
    }

    public function setFont(int $fontSize): static
    {
        if ($fontSize < 0) {
            $fontSize = 0;
        }
        if ($fontSize > 4) {
            $fontSize = 4;
        }

        $this->fontSize = $fontSize;
        return $this;
    }

    public function setIntensity(int $intensity): static
    {

        if ($intensity < 0) {
            $intensity = 0;
        }
        if ($intensity > 15) {
            $intensity = 15;
        }

        $this->intensity = $intensity;
        return $this;
    }
    public function toArray(): array
    {
        return [
            'PrintText' => [
                'Text' => $this->text,
                'Font' => $this->fontSize,
                'Intensity' => $this->intensity
            ]
        ];
    }
}
