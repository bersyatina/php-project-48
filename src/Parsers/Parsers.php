<?php

namespace Parsers\Parsers;

use Symfony\Component\Yaml\Yaml;

function decode(string $pathToFile): array
{
    if (is_file($pathToFile)) {
        $file = $pathToFile;
    } elseif(is_file(__DIR__ . "/" . $pathToFile)) {
        $file = __DIR__ . "/" . $pathToFile;
    } else {
        return ['File not found!'];
    }

    $pathInfo = pathinfo($pathToFile);
    $extension = array_key_exists('extension', $pathInfo) ? $pathInfo['extension'] : '';

    switch ($extension) {
        case 'json':
            return json_decode((string) file_get_contents($file), true);
        case 'yaml':
        case 'yml':
            return Yaml::parseFile($file);
    }
    return ["Files with the extension '{$extension}' are not supported"];
}

function getComparison(string $value, array $first = [], array $second = []): array
{
    if (array_key_exists($value, $first) && array_key_exists($value, $second)) {
        if (!is_array($first[$value]) || !is_array($second[$value])) {
            if ($first[$value] === $second[$value]) {
                return [
                    'operator' => ' ',
                    'key' => $value,
                    'value' => $first[$value]
                ];
            } else {
                return [
                    'replaced_array' => [
                        [
                            'operator' => '-',
                            'key' => $value,
                            'value' => $first[$value]
                        ],
                        [
                            'operator' => '+',
                            'key' => $value,
                            'value' => $second[$value]
                        ]
                    ]
                ];
            }
        }
        return [
            'operator' => ' ',
            'key' => $value,
            'value' => $first[$value]
        ];
    } elseif (array_key_exists($value, $first) && !array_key_exists($value, $second)) {
        return [
            'operator' => '-',
            'key' => $value,
            'value' => $first[$value]
        ];
    } elseif (!array_key_exists($value, $first) && array_key_exists($value, $second)) {
        return [
            'operator' => '+',
            'key' => $value,
            'value' => $second[$value]
        ];
    }
    return [];
}

function getDataStatus(array $data): bool
{
    return (array_key_exists('operator', $data) && array_key_exists('key', $data) && array_key_exists('value', $data));
}

function toString(mixed $value): string
{
    return trim(var_export($value, true), "'") === "NULL" ? "null" : trim(var_export($value, true), "'");
}

function arraySort(array $array, array $resultArray = []): array
{
    if (count($array) > 0) {
        $minValue = min($array);
        $newArray = $array;
        unset($newArray[array_search($minValue, $newArray, true)]);
        $newResultArray = array_merge($resultArray, [$minValue]);
        return arraySort($newArray, $newResultArray);
    }
    return $resultArray;
}

function generateKeys(array $firstArr, array $secondArr): array
{
    $result = array_unique(array_merge(array_keys($firstArr), array_keys($secondArr)));

    return arraySort($result);
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

        if (count($newReplacedKeys) > 1) {
            return getReplacedArray($newArray, array_slice($newReplacedKeys, 1));
        }
        return $newArray;
    }

    if (count($replacedKeys) > 1) {
        return getReplacedArray($array, array_slice($replacedKeys, 1));
    }
    return $array;
}

function getResultToArray(array $filesKeys, array $firstFileArr, array $secondFileArr): array
{
    $result = array_map(function ($value) use ($firstFileArr, $secondFileArr) {
        if (array_key_exists($value, $firstFileArr) && array_key_exists($value, $secondFileArr)) {
            if (is_array($firstFileArr[$value]) && is_array($secondFileArr[$value])) {
                $arrayKeys = generateKeys($firstFileArr[$value], $secondFileArr[$value]);
                $comparison = getComparison($value, $firstFileArr, $secondFileArr);
                return [
                    'operator' => $comparison['operator'],
                    'key' => $comparison['key'],
                    'value' => getResultToArray($arrayKeys, $firstFileArr[$value], $secondFileArr[$value])
                ];
            }
        }
        return getComparison($value, $firstFileArr, $secondFileArr);
    }, $filesKeys);

    $clearResult = array_values($result);

    $replacedKeys = array_map(function ($item) use ($clearResult) {
        if (array_key_exists('replaced_array', $item)) {
            return array_search($item, $clearResult, true);
        }
    }, $clearResult);
    return getReplacedArray($clearResult, $replacedKeys);
}
