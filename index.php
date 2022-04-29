<?php

require_once 'vendor/autoload.php';

function dump($value)
{
    echo '<pre>';
    var_export($value);
    echo '</pre>';
}


$parser = new IsHleb\Parser\Parser();

$collection = $parser->loadL1(); // Модели
dump($collection);
// $node = $collection->toObjectArray()[0];
// $node = $parser->parseNext($node); // Года
// $node = $node->getSubNodes()->toObjectArray()[0];
// $node = $parser->parseNext($node); // Модели по годам
// $node = $node->getSubNodes()->toObjectArray()[0];
// $node = $parser->parseNext($node); // Запчасти (Типа двигатель)
// $node = $node->getSubNodes()->toObjectArray()[0];
// $node = $parser->parseNext($node); // Запчасти типа (Верх/низ двигателя)
// $node = $node->getSubNodes()->toObjectArray()[sizeof($node->getSubNodes()->toObjectArray()) - 1];
// $node = $parser->parseNext($node); // Ссылки на запчасти
// $node = $node->getSubNodes()->toObjectArray()[0]; // load l7
// dump($node);
// $node = $parser->parseNext($node); // Таблица
// file_put_contents('output.json', $node->getValue()->toJson());
// dump($node);

