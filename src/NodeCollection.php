<?php
namespace IsHleb\Parser;

class NodeCollection {

    private array $nodes;

    public function push(Node $node) {
        $this->nodes[] = $node;
    }

    public function toObjectArray(): array
    {
        return $this->nodes;
    }

    public function toArray(): array
    {
        $output = [];
        foreach ($this->nodes as $node) {
            $output[] = $node->toArray();
        }
        return $output;
    }

    public function toJson(): bool|string
    {
        return json_encode($this->toArray());
    }

}