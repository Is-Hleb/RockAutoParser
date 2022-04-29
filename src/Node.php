<?php
namespace IsHleb\Parser;


class Node
{

    const FIRST_LEVEL = 1;
    const SECOND_LEVEL = 2;
    const THIRD_LEVEL = 3;
    const FOURTH_LEVEL = 4;
    const FIVES_LEVEL = 5;
    const SIXTH_LEVEL = 6;
    const SEVENTH_LEVEL = 7;
    const FINISHED = 8;

    private string $url;
    private string|CarPartsCollection $value;
    private int $level;
    public array $payload;
    private int $collectionSize;
    private NodeCollection $subNodes;

    public function __construct($url, $value, $level, $payload = "", $collectionSize = 0)
    {
        $this->collectionSize = $collectionSize;
        $this->url = $url;
        $this->value = $value;
        $this->level = $level;
        $this->payload = json_decode(htmlspecialchars_decode($payload), true) ?? [];
        $this->subNodes = new NodeCollection();
    }

    public function getCollectionSize(): mixed
    {
        return $this->collectionSize;
    }

    public function toJson(): bool|string
    {
        return json_encode($this->toArray());
    }


    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'value' => $this->value,
            'level' => $this->level
        ];
    }


    public function getValue(): string|CarPartsCollection
    {
        return $this->value;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setSubNodes(NodeCollection $subNodes): void
    {
        $this->subNodes = $subNodes;
    }

    public function getSubNodes() : NodeCollection {
        return $this->subNodes;
    }
}