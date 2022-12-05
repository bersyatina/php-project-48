<?php

namespace Parsers\Parsers;

use Symfony\Component\Yaml\Yaml;

function decode(string $pathToFile): array
{
    if (!is_file($pathToFile)) {
        return ['File not found!'];
    }

    $pathInfo = pathinfo($pathToFile);
    $extension = array_key_exists('extension', $pathInfo) ? $pathInfo['extension'] : '';

    switch ($extension) {
        case 'json':
            return json_decode((string) file_get_contents($pathToFile), true);
        case 'yaml':
        case 'yml':
            return Yaml::parseFile($pathToFile);
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
                    'replaced_array',
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
        $newResultArray = $resultArray;
        $newResultArray[] = $minValue;
        return arraySort($newArray, $newResultArray);
    }
    return $resultArray;
}

function generateKeys(array $firstArr, array $secondArr): array
{
    $result = array_unique(array_merge(array_keys($firstArr), array_keys($secondArr)));

    return arraySort($result);
}

function getReplacedArray(array $array)
{
    $newArray = [];
    array_filter($array, function ($item) use (&$newArray) {
        if (array_key_exists(0, $item) && $item[0] === 'replaced_array') {
            $newArray[] = $item[1];
            $newArray[] = $item[2];
        } else {
            $newArray[] = $item;
        }
    });
    return $newArray;
}

function getResultToArray(array $filesKeys, array $firstFileArr, array $secondFileArr): array
{
    $result = array_map(function ($value) use ($firstFileArr, $secondFileArr) {
        if (array_key_exists($value, $firstFileArr) && array_key_exists($value, $secondFileArr)) {
            if (is_array($firstFileArr[$value]) && is_array($secondFileArr[$value])) {
                $arrayKeys = generateKeys($firstFileArr[$value], $secondFileArr[$value]);

                $acc = getComparison($value, $firstFileArr, $secondFileArr);
                $acc['value'] = getResultToArray($arrayKeys, $firstFileArr[$value], $secondFileArr[$value]);
                return $acc;
            }
        }
        return getComparison($value, $firstFileArr, $secondFileArr);
    }, $filesKeys);

    $clearResult = array_values($result);
    return getReplacedArray($clearResult);
}
