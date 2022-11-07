<?php

namespace Differ\Differ;

use function Parsers\Parsers\decode;

function getComparison($value, $first = [], $second = []): array
{
    if (array_key_exists($value, $first) && array_key_exists($value, $second)) {
        if (!is_array($first[$value]) || !is_array($second[$value])) {
            if ($first[$value] === $second[$value]) {
                return [' ', $value, $first[$value]];
            } else {
                return [['-', $value, $first[$value]], ['+', $value, $second[$value]]];
            }
        }
        return [' ', $value, $first[$value]];
    } elseif (array_key_exists($value, $first) && !array_key_exists($value, $second)) {
        return ['-', $value, $first[$value]];
    } elseif (!array_key_exists($value, $first) && array_key_exists($value, $second)) {
        return ['+', $value, $second[$value]];
    }
    return [];
}

function suitableValue(mixed $value, mixed $fileArr): bool
{
    return (array_key_exists($value[1], $fileArr) && is_array($fileArr[$value[1]]));
}

function getResultString(array $filesKeys, array $firstFileArr, array $secondFileArr)
{
    $result = array_map(function ($value) use ($firstFileArr, $secondFileArr){
        if (array_key_exists($value, $firstFileArr) && array_key_exists($value, $secondFileArr)){
            if (is_array($firstFileArr[$value]) && is_array($secondFileArr[$value])) {
                $arrayKeys = array_unique(array_merge(array_keys($firstFileArr[$value]), array_keys($secondFileArr[$value])));
                sort($arrayKeys);
                $item = getComparison($value, $firstFileArr, $secondFileArr);
                $item[2] = getResultString($arrayKeys, $firstFileArr[$value], $secondFileArr[$value]);
                return $item;
            }
        }
        return getComparison($value, $firstFileArr, $secondFileArr);
    }, $filesKeys);

    return $result;
//    $res = "";
//    foreach ($result as $item) {
//        $res .= "  " . $item[0] . " " . str_replace('"', "", json_encode([$item[1] => $item[2]]));
//    }
//    $res .= "";
//
//    $res = str_replace("}", "\n", $res);
//    $res = str_replace("{", "", $res);
//    $res = str_replace(":", ": ", $res);
//
//    return "{\n" . $res . "}";
}

function genDiff(string $firstFile, string $secondFile, string $format = 'json'): string
{
    $pathToFiles = dirname(__DIR__, 1) . '/files/';

    $firstFileArr = decode($pathToFiles . $firstFile);
    $secondFileArr = decode($pathToFiles . $secondFile);

    $filesKeys = array_unique(array_merge(array_keys($firstFileArr), array_keys($secondFileArr)));
    sort($filesKeys);
    dd(getResultString($filesKeys, $firstFileArr, $secondFileArr));
    return getResultString($filesKeys, $firstFileArr, $secondFileArr);
}
