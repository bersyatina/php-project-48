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

function getResultToString(array $fileArr): string
{
    $res = "";
    foreach ($fileArr as $item) {
        $res .= "  " . $item[0] . " " . str_replace('"', "", json_encode([$item[1] => $item[2]]));
    }
    $res .= "";
    $res = str_replace("}", "\n", $res);
    $res = str_replace("{", "", $res);
    $res = str_replace(":", ": ", $res);
    return "{\n" . $res . "}";
}

function generateKeys(array $firstArr, array $secondArr): array
{
    $result = array_unique(array_merge(array_keys($firstArr), array_keys($secondArr)));
    sort($result);
    return $result;
}

function getResultToArray(array $filesKeys, array $firstFileArr, array $secondFileArr)
{
    $result =array_reduce ($filesKeys, function ($acc, $value) use ($firstFileArr, $secondFileArr) {
        if (array_key_exists($value, $firstFileArr) && array_key_exists($value, $secondFileArr)) {
            if (is_array($firstFileArr[$value]) && is_array($secondFileArr[$value])) {
                $arrayKeys = generateKeys($firstFileArr[$value], $secondFileArr[$value]);

                $acc[$value] = getComparison($value, $firstFileArr, $secondFileArr);
                $acc[$value][2] = getResultToArray($arrayKeys, $firstFileArr[$value], $secondFileArr[$value]);
                return $acc
;            }
        }
        $res1 = getComparison($value, $firstFileArr, $secondFileArr);

        if (count($res1) === 2) {
            $acc[] = $res1[0];
            $acc[] = $res1[1];
        } else {
            $acc[] = $res1;
        }
        return $acc;
    }, []);
    return array_values($result);
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


function genDiff(string $firstFile, string $secondFile, string $format = 'json')
{
    $pathToFiles = dirname(__DIR__, 1) . '/files/';

    $firstFileArr = decode($pathToFiles . $firstFile);
    $secondFileArr = decode($pathToFiles . $secondFile);
    $filesKeys = generateKeys($firstFileArr, $secondFileArr);

    $arrResult = getResultToArray($filesKeys, $firstFileArr, $secondFileArr);

    return getResultToString($arrResult);
}
