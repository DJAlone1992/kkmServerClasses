<?php

namespace Djalone\KkmServerClasses\Cheque\Items;

class Image extends Item
{
    private readonly string $image;

    public function __construct($image)
    {
        if (file_exists($image)) {
            $buffer = file_get_contents($image);
        }
        $this->image = base64_encode($buffer);
    }
    public function toArray(): array
    {
        return [
            'PrintImage' => [
                'Image' => $this->image
            ]
        ];
    }
}
