<?php

namespace Differ\Differ;

use function Parsers\Parsers\decode;
use function Formatters\format;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{

    $firstFileArr = decode($firstFile);
    $secondFileArr = decode($secondFile);
    $filesKeys = generateKeys($firstFileArr, $secondFileArr);

    $arrResult = getResultToArray($filesKeys, $firstFileArr, $secondFileArr);

    return format($arrResult, $format);
}

function getComparison(string $value, array $first = [], array $second = []): array
{
    if (isKeyExistsInArrays($value, $first, $second)) {
        if (isKeyNotExistsInArrays($value, $first, $second)) {
            return $first[$value] === $second[$value]
                ? ['operator' => ' ', 'key' => $value, 'value' => $first[$value]]
                : ['replaced_array' => [
                    ['operator' => '-', 'key' => $value, 'value' => $first[$value]],
                    ['operator' => '+', 'key' => $value, 'value' => $second[$value]]]
                ];
        }
        return ['operator' => ' ', 'key' => $value, 'value' => $first[$value]];
    } elseif (isKeyExistsInOneArray($value, $first, $second)) {
        return ['operator' => '-', 'key' => $value, 'value' => $first[$value]];
    } elseif (isKeyExistsInOneArray($value, $second, $first)) {
        return ['operator' => '+', 'key' => $value, 'value' => $second[$value]];
    }
    return [];
}

function getResultToArray(array $filesKeys, array $firstFileArr, array $secondFileArr): array
{
    $result = array_map(function ($value) use ($firstFileArr, $secondFileArr) {
        if (isValidArrays($value, $firstFileArr, $secondFileArr)) {
            $arrayKeys = generateKeys($firstFileArr[$value], $secondFileArr[$value]);
            $comparison = getComparison($value, $firstFileArr, $secondFileArr);
            return [
                'operator' => $comparison['operator'],
                'key' => $comparison['key'],
                'value' => getResultToArray($arrayKeys, $firstFileArr[$value], $secondFileArr[$value])
            ];
        }
        return getComparison($value, $firstFileArr, $secondFileArr);
    }, $filesKeys);

    $replacedKeys = getReplacedKeys($result);

    return getReplacedArray($result, $replacedKeys);
}

function isValidArrays(string $value, array $firstFileArr, array $secondFileArr): bool
{
    return array_key_exists($value, $firstFileArr)
        && array_key_exists($value, $secondFileArr)
        && is_array($firstFileArr[$value])
        && is_array($secondFileArr[$value]);
}

function getReplacedKeys(array $array): array
{
    return array_map(function ($item) use ($array) {
        if (array_key_exists('replaced_array', $item)) {
            return array_search($item, $array, true);
        }
    }, $array);
}

function getReplacedArray(array $array, array $replacedKeys): array
{
    if ($replacedKeys[0] !== null) {
        $firstArray = array_slice($array, 0, $replacedKeys[0]);
        $secondArray = array_slice($array, $replacedKeys[0] + 1, count($array));

        $newArray = array_merge($firstArray, $array[$replacedKeys[0]]['replaced_array'], $secondArray);

        $newReplacedKeys = array_map(function ($key) {
            return is_int($key) ? $key + 1 : $key;
        }, $replacedKeys);

        return reduceArray($newReplacedKeys, $newArray);
    }

    return reduceArray($replacedKeys, $array);
}

function reduceArray(array $replacedKeys, array $array): array
{
    if (count($replacedKeys) > 1) {
        return getReplacedArray($array, array_slice($replacedKeys, 1));
    }
    return $array;
}

function generateKeys(array $firstArr, array $secondArr): array
{
    $result = array_unique(array_merge(array_keys($firstArr), array_keys($secondArr)));

    return arraySort($result);
}

function arraySort(array $array, array $resultArray = []): array
{
    if (count($array) === 0) {
        return $resultArray;
    }
    $minValue = min($array);
    $newArray = $array;
    unset($newArray[array_search($minValue, $newArray, true)]);
    $newResultArray = array_merge($resultArray, [$minValue]);
    return arraySort($newArray, $newResultArray);
}


function isKeyExistsInArrays(string $key, array $first, array $second): bool
{
    return array_key_exists($key, $first) && array_key_exists($key, $second);
}

function isKeyExistsInOneArray(string $key, array $arrayWithKey, array $arrayWithoutKey): bool
{
    return array_key_exists($key, $arrayWithKey) && !array_key_exists($key, $arrayWithoutKey);
}

function isKeyNotExistsInArrays(string $key, array $first, array $second): bool
{
    return !is_array($first[$key]) || !is_array($second[$key]);
}

function toString(mixed $value): string
{
    return trim(var_export($value, true), "'") === "NULL"
        ? "null"
        : trim(var_export($value, true), "'");
}

function isComparisonArray(array $data): bool
{
    return (array_key_exists('operator', $data)
        && array_key_exists('key', $data)
        && array_key_exists('value', $data));
}
