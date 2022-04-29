<?php

namespace IsHleb\Parser;

class PartInfo
{
    public string $brand = ""; // 1
    public string $partNumber = ''; // 2
    public string $oemNumber = ''; // 3
    public string $moreInfoText = ''; // 4
    public string $price = ''; // 5
    public array $imagesUrls = []; // 5
    public string $moreInfoUrl = '';

    public function toArray() : array
    {
        return [
            'brand' => $this->brand,
            'partNumber' => $this->partNumber,
            'oemNumber' => $this->oemNumber,
            'moreInfoText' => $this->moreInfoText,
            'price' => $this->price,
            'imagesUrls' => $this->imagesUrls,
            'moreInfoUrl' => $this->moreInfoUrl,
        ];
    }

}