<?php

namespace Differ\Differ;

use function Parsers\Parsers\decode;

define("TARGET", 'target');

function getComparison($value, $first = [], $second = []): array
{
    if (array_key_exists($value, $first) && array_key_exists($value, $second)) {
        if (!is_array($first[$value]) || !is_array($second[$value])) {
            if ($first[$value] === $second[$value]) {
                return [TARGET, ' ', $value, $first[$value]];
            } else {
                return [[TARGET, '-', $value, $first[$value]], [TARGET, '+', $value, $second[$value]]];
            }
        }
        return [TARGET, ' ', $value, $first[$value]];
    } elseif (array_key_exists($value, $first) && !array_key_exists($value, $second)) {
        return [TARGET, '-', $value, $first[$value]];
    } elseif (!array_key_exists($value, $first) && array_key_exists($value, $second)) {
        return [TARGET, '+', $value, $second[$value]];
    }
    return [];
}

function getResultToString(array $fileArr): string
{
    $res = '';
    foreach ($fileArr as $target) {
        if (is_array($target)){
            if (in_array(TARGET, $target)) {
                dump("Вывод", $target[2], $target[3]);
                array_shift($target);
                return getResultToString($target);
            } else {
                return getResultToString($target);
            }
        }
//        array_shift($target);
//        dump("ПОСЛЕ", $target);
        $res .= json_encode($target);
    }

    return $res;
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
                $acc[$value][3] = getResultToArray($arrayKeys, $firstFileArr[$value], $secondFileArr[$value]);
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