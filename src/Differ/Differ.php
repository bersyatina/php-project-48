<?php

namespace Differ\Differ;

use function Parsers\Parsers\decode;

define("TARGET", 'target');

function getComparison($value, $first = [], $second = []): array
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

function getDataStatus (array $data): bool
{
    return (array_key_exists('operator', $data) && array_key_exists('key', $data) && array_key_exists('value', $data));
}

function getResultToString(array $array, $depth = 1): string
{
    $result = array_map(function ($item) use (&$result, $depth, $array){
        $currentIndent = str_repeat('  ', $depth);
        $bracketIndent = str_repeat('  ', $depth - 1);

        if (is_array($item)) {
            if (!getDataStatus($item)){
                return array_search($item, $array). ": " . getResultToString($item, $depth + 1);
            } else {
                if (is_array($item['value'])) {
                    return "{$currentIndent}{$item['operator']} {$item['key']}: " . getResultToString($item['value'], $depth + 1);
                } else {
                    return "{$currentIndent}{$item['operator']} {$item['key']}: " . $item['value'];
                }
            }
        }
//        dump(array_search($item, $array));
        return array_search($item, $array). ": " . $item;
    }, $array);

    return implode("\n", $result);
}


function generateKeys(array $firstArr, array $secondArr): array
{
    $result = array_unique(array_merge(array_keys($firstArr), array_keys($secondArr)));
    sort($result);
    return $result;
}

function getResultToArray(array $filesKeys, array $firstFileArr, array $secondFileArr)
{
    $result = array_reduce($filesKeys, function ($acc, $value) use ($firstFileArr, $secondFileArr) {
        if (array_key_exists($value, $firstFileArr) && array_key_exists($value, $secondFileArr)) {
            if (is_array($firstFileArr[$value]) && is_array($secondFileArr[$value])) {
                $arrayKeys = generateKeys($firstFileArr[$value], $secondFileArr[$value]);

                $acc[$value] = getComparison($value, $firstFileArr, $secondFileArr);
                $acc[$value]['value'] = getResultToArray($arrayKeys, $firstFileArr[$value], $secondFileArr[$value]);
                return $acc;
            }
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
