<?php

namespace IsHleb\Parser;

class CarPartsCollection {
    private array $parts;
    public string $brandList;
    public string $partName;

    public function push(PartInfo $partInfo) {
        $this->parts[] = $partInfo;
    }

    public function toObjectArray(): array
    {
        return $this->parts;
    }

    public function toArray() : array{
        $parts = [];
        foreach ($this->parts as $part) {
            $parts[] = $part->toArray();
        }
        return [
            'brandList' => $this->brandList,
            $this->partName => $parts
        ];
    }

    public function toJson() : string {
        return json_encode($this->toArray());
    }

}