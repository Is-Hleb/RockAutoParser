<?php

namespace IsHleb\Parser;

use PHPHtmlParser\Dom;

class Parser
{
    protected const BASE_URL = "https://www.rockauto.com/en/";
    protected Dom $domParser;

    public function getPartsByNumber(string $number): CarPartsCollection
    {
        $url = "https://www.rockauto.com/en/partsearch/?partnum=".$number;
        $body = Request::getBody($url);
        $this->domParser->loadStr($body);
        // echo $body;
        $collection = new CarPartsCollection();


        $blocks = $this->domParser->find('.listing-inner');
        foreach ($blocks as $block) {
            $partInfo = new PartInfo();

            // Brand
            $partInfo->brand = $block->find('.listing-final-manufacturer', 0)->text;
            // End brand

            // Part Number
            $partInfo->partNumber = $block->find('span.listing-final-partnumber', 0)->text;
            // End Part Number

            // Oem Number
            $oemSpan = $block->find('div.listing-text-row-moreinfo-truck', 0)->find('span', 2);
            if($oemSpan) {
                $partInfo->oemNumber = $oemSpan->text;
            }
            // End Oem Number

            // Price
            $partInfo->price = $block->find('span.ra-formatted-amount', 0)->find('span', 0)->text;
            // End price

            // images
            $images = $block->find('img.listing-inline-image');
            $inputs = $block->find('input[type=hidden]');
            foreach ($inputs as $input) {
                $value = $input->value;
                $value = json_decode(htmlspecialchars_decode($value), true);
                if ($value) {
                    if (!isset($value['Slots'])) continue;
                    foreach ($value['Slots'] as $slot) {
                        foreach ($slot['ImageData'] as $src) {
                            $src = 'https://www.rockauto.com' . $src;
                            if (!in_array($src, $partInfo->imagesUrls))
                                $partInfo->imagesUrls[] = $src;
                        }
                    }
                }
            }
            foreach ($images as $image) {
                $src = 'https://www.rockauto.com' . $image->getAttribute('src');
                if (!in_array($src, $partInfo->imagesUrls))
                    $partInfo->imagesUrls[] = $src;
            }
            // end images

            // More Info Text
            $partOne = $block->find('span.span-link-underline-remover', 0);
            $partOne = $partOne ? $partOne->text : "";
            $partTwo = $block->find('div.listing-text-row', 0)->find('span', 0)->find('span', 0);
            $partTwo = $partTwo ? $partTwo->text : "";
            $partInfo->moreInfoText = trim($partOne . ' ' . $partTwo);
            // End More Info Text

            // Info link
            $link = $block->find('a.ra-btn-moreinfo', 0)->getAttribute('href');
            $partInfo->moreInfoUrl = $link;
            // End info link

            $collection->push($partInfo);
        }

        return $collection;
    }

    public function __construct()
    {
        $this->domParser = new Dom;
    }

    public function loadL1(): NodeCollection
    {
        $body = Request::getBody(self::BASE_URL);
        $this->domParser->loadStr($body);
        $blocks = $this->domParser->find('.ranavnode');

        $collection = new NodeCollection();

        foreach ($blocks as $block) {
            $payload = $block->find('input[type=hidden]', 0)->value;
            $link = $block->find('.nlabel', 0)->find('a', 0);
            $collection->push(new Node(
                self::BASE_URL . substr($link->getAttribute('href'), 4),
                $link->text,
                Node::FIRST_LEVEL,
                $payload,
                sizeof($blocks)
            ));
        }
        return $collection;
    }


    private function loadL2(Node $node): NodeCollection
    {
        $payload = [
            'jsn' => array_merge($node->payload, [
                'label' => $node->getValue(),
                'href' => $node->getUrl(),
                "labelset" => true,
                "ok_to_expand_single_child_node" => true,
                "bring_listings_into_view" => true,
                "loaded" => false,
                "expand_after_load" => true,
                "fetching" => true
            ]),
            'max_group_index' => $node->getCollectionSize(),
        ];
        $body = Request::postBody($payload);
        $this->domParser->loadStr($body);

        $blocks = $this->domParser->find('.ranavnode');

        $collection = new NodeCollection();

        $index = 1;
        foreach ($blocks as $block) {
            $payload = $block->find('input[type=hidden]', 0)->value;
            $link = $block->find('.nlabel', 0)->find('a', 0);

            $newNode = new Node(
                self::BASE_URL . substr($link->getAttribute('href'), 4),
                $link->text,
                Node::SECOND_LEVEL,
                $payload,
                sizeof($blocks)
            );
            $newNode->payload['groupindex'] = sizeof($blocks) + $node->getCollectionSize() + $node->payload['groupindex'] + $index++;
            $collection->push($newNode);
        }

        return $collection;
    }

    private function loadL3(Node $node): NodeCollection
    {
        $payload = [
            'jsn' => array_merge($node->payload, [
                'label' => $node->getValue(),
                'href' => $node->getUrl(),
                "labelset" => true,
                "ok_to_expand_single_child_node" => true,
                "bring_listings_into_view" => true,
                "loaded" => false,
                "expand_after_load" => true,
                "fetching" => true
            ]),
            'max_group_index' => $node->getCollectionSize(),
        ];
        $body = Request::postBody($payload);
        $this->domParser->loadStr($body);
        $blocks = $this->domParser->find('.ranavnode');

        $collection = new NodeCollection();

        $index = 1;
        foreach ($blocks as $block) {
            $payload = $block->find('input[type=hidden]', 0)->value;
            $link = $block->find('.nlabel', 0)->find('a', 0);

            $newNode = new Node(
                self::BASE_URL . substr($link->getAttribute('href'), 4),
                $link->text,
                Node::THIRD_LEVEL,
                $payload,
                sizeof($blocks)
            );
            $newNode->payload['groupindex'] = $node->payload['groupindex'] + $index++;
            $newNode->payload['parentgroupindex'] += sizeOf($blocks);
            $collection->push($newNode);
        }

        return $collection;
    }


    private function loadL4(Node $node, $level): NodeCollection
    {
        $payload = [
            'jsn' => array_merge($node->payload, [
                'label' => $node->getValue(),
                'href' => $node->getUrl(),
                "labelset" => true,
                "ok_to_expand_single_child_node" => true,
                "bring_listings_into_view" => true,
                "loaded" => false,
                "expand_after_load" => true,
                "fetching" => true
            ]),
            'max_group_index' => $node->getCollectionSize(),
        ];
        $body = Request::postBody($payload);
        $this->domParser->loadStr($body);

        $blocks = $this->domParser->find('.ranavnode');

        $collection = new NodeCollection();

        $index = 1;
        foreach ($blocks as $block) {
            $payload = $block->find('input[type=hidden]', 0)->value;
            $link = $block->find('.nlabel', 0)->find('a', 0);

            $newNode = new Node(
                self::BASE_URL . substr($link->getAttribute('href'), 4),
                $link->text,
                $level,
                $payload,
                sizeof($blocks)
            );
            $newNode->payload['groupindex'] = $node->payload['groupindex'] + $index++;
            $newNode->payload['parentgroupindex'] += sizeOf($blocks);
            $collection->push($newNode);
        }

        return $collection;
    }

    public function l7(Node $node): Node
    {
        $payload = [
            'jsn' => array_merge($node->payload, [
                'label' => $node->getValue(),
                'href' => $node->getUrl(),
                "labelset" => true,
                "ok_to_expand_single_child_node" => true,
                "bring_listings_into_view" => true,
                "loaded" => false,
                "expand_after_load" => true,
                "fetching" => true
            ]),
            'max_group_index' => $node->getCollectionSize(),
        ];
        $body = Request::postBody($payload);



        $this->domParser->loadStr($body);
        $collection = new CarPartsCollection();

        $blocks = $this->domParser->find('.nobmp tbody');


        // dump($blocks);
        // Get Brand List
        $tacenter = $blocks[0]->find('.tacenter');
        $ibs = $tacenter->find('.ib');
        $brandList = [];
        for ($i = 0; $i < sizeof($ibs) - 1; $i++) {
            $ib = $ibs[$i];
            $span = $ib->find('span')[0];
            $text = $span->text;
            $brandList[] = $text;
        }
        $collection->brandList = implode(" > ", $brandList);
        // End Brand List

        // Part Name
        $collection->partName = $ibs[sizeof($ibs) - 1]->find('span')[0]->text;
        // End part Name


        $blocks = $this->domParser->find('.listing-inner');
        foreach ($blocks as $block) {
            $partInfo = new PartInfo();

            // Brand
            $partInfo->brand = $block->find('.listing-final-manufacturer', 0)->text;
            // End brand

            // Part Number
            $partInfo->partNumber = $block->find('span.listing-final-partnumber', 0)->text;
            // End Part Number

            // Oem Number
            $oemSpan = $block->find('div.listing-text-row-moreinfo-truck', 0)->find('span', 2);
            if($oemSpan) {
                $partInfo->oemNumber = $oemSpan->text;
            }
            // End Oem Number

            // Price
            $partInfo->price = $block->find('span.ra-formatted-amount', 0)->find('span', 0)->text;
            // End price

            // images
            $images = $block->find('img.listing-inline-image');
            $inputs = $block->find('input[type=hidden]');
            foreach ($inputs as $input) {
                $value = $input->value;
                $value = json_decode(htmlspecialchars_decode($value), true);
                if ($value) {
                    if (!isset($value['Slots'])) continue;
                    foreach ($value['Slots'] as $slot) {
                        foreach ($slot['ImageData'] as $src) {
                            $src = 'https://www.rockauto.com' . $src;
                            if (!in_array($src, $partInfo->imagesUrls))
                                $partInfo->imagesUrls[] = $src;
                        }
                    }
                }
            }
            foreach ($images as $image) {
                $src = 'https://www.rockauto.com' . $image->getAttribute('src');
                if (!in_array($src, $partInfo->imagesUrls))
                    $partInfo->imagesUrls[] = $src;
            }
            // end images

            // More Info Text
            $partOne = $block->find('span.span-link-underline-remover', 0);
            $partOne = $partOne ? $partOne->text : "";
            $partTwo = $block->find('div.listing-text-row', 0)->find('span', 0)->find('span', 0);
            $partTwo = $partTwo ? $partTwo->text : "";
            $partInfo->moreInfoText = trim($partOne . ' ' . $partTwo);
            // End More Info Text

            // Info link
            $link = $block->find('a.ra-btn-moreinfo', 0)->getAttribute('href');
            $partInfo->moreInfoUrl = $link;
            // End info link

            $collection->push($partInfo);
        }


        return new Node($node->getUrl(), $collection, Node::FINISHED);
    }

    public function parseNext(Node $node): Node
    {
        switch ($node->getLevel()) {
            case Node::FIRST_LEVEL:
                $node->setSubNodes($this->loadL2($node));
                break;
            case Node::SECOND_LEVEL:
                $node->setSubNodes($this->loadL3($node));
                break;
            case Node::THIRD_LEVEL:
                $node->setSubNodes($this->loadL4($node, NODE::FOURTH_LEVEL));
                break;
            case Node::FOURTH_LEVEL:
                $node->setSubNodes($this->loadL4($node, NODE::FIVES_LEVEL));
                break;
            case Node::FIVES_LEVEL:
                $node->setSubNodes($this->loadL4($node, NODE::SIXTH_LEVEL));
                break;
            case Node::SIXTH_LEVEL:
                return $this->l7($node);
        }
        return $node;
    }
}