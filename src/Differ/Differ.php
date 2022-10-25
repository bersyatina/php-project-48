<?php

namespace Differ\Differ;

use Docopt;

$doc = <<<DOCOPT
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>
  
Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]
DOCOPT;

$result = Docopt::handle($doc, array('version'=>'1.0.0rc2'));
foreach ($result as $k=>$v)
    echo $k.': '.json_encode($v).PHP_EOL;


function decode($pathToFile)
{
    if (is_file($pathToFile)) {
        return json_decode(file_get_contents($pathToFile), 1);
    } else {
        return ['Файл не существует'];
    }
}

function getValue($array = null)
{
    if (!empty($array)) {
        return $array[1];
    }
    return ' ';
}

function getArray($array, $operator)
{
    return [$operator, $array[0], $array[1]];
}

function getComparison($first = [], $second = []): array
{
    $result = [];
    $arrayValues = [
        'first' => $first,
        'second' => $second
    ];
    switch ($arrayValues) {
        case $arrayValues['first'] !== [] && $arrayValues['second'] === []:
            $result[] = getArray($first, '-');
            break;
        case $arrayValues['first'] === [] && $arrayValues['second'] !== []:
            $result[] = getArray($second, '+');
            break;
        case $arrayValues['first'] !== [] && $arrayValues['second'] !== []:
            if (getValue($arrayValues['first']) === getValue($arrayValues['second'])){
                $result[] = getArray($first, ' ');
                break;
            }
            $result[] = getArray($first, '-');
            $result[] = getArray($second, '+');
            break;
    }

    return $result;
}

function genDiff(string $firstFile, string $secondFile, $format = null): void
{
    $pathToFiles = dirname(__DIR__, 1) . '/files/';

    $firstFileArr = decode($pathToFiles . $firstFile);
    $secondFileArr = decode($pathToFiles . $secondFile);

    $filesKeys = array_unique(array_merge(array_keys($firstFileArr), array_keys($secondFileArr)));
    sort($filesKeys);

    $result = [];
    foreach ($filesKeys as $key => $value) {
        $first = array_key_exists($value, $firstFileArr) ? [$value, $firstFileArr[$value]]: [];
        $second = array_key_exists($value, $secondFileArr) ? [$value, $secondFileArr[$value]]: [];
        $arrayComparison = getComparison($first, $second);
        $result[] = $arrayComparison;
    }
    $result = array_merge(...$result);

    $res = "";
    foreach ($result as $item) {
        $res .= $item[0] . " " . json_encode([$item[1] => $item[2]]);
    }
    $res .= "";

    $res = str_replace("}", ",\n", $res);
    $res = str_replace("{", "", $res);
    $res = str_replace(":", ": ", $res);

    print_r($res);
}
