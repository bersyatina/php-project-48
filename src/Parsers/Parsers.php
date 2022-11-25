<?php

namespace Parsers\Parsers;

use Symfony\Component\Yaml\Yaml;

function decode($pathToFile)
{
    if (!is_file($pathToFile)) {
        return ['Файл не существует'];
    }

    $info = pathinfo($pathToFile);

    switch ($info['extension']) {
        case 'json':
            return json_decode(file_get_contents($pathToFile), true);
        case 'yaml' || 'yml':
            return Yaml::parseFile($pathToFile);
    }
    return ["Файлы с расширением '{$info['extension']}' не поддерживается"];
}

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

function getDataStatus(array $data): bool
{
    return (array_key_exists('operator', $data) && array_key_exists('key', $data) && array_key_exists('value', $data));
}

function toString($value): string
{
    return trim(var_export($value, true), "'") === "NULL" ? "null" : trim(var_export($value, true), "'");
}

function generateKeys(array $firstArr, array $secondArr): array
{
    $result = array_unique(array_merge(array_keys($firstArr), array_keys($secondArr)));
    sort($result);
    return $result;
}

function getResultToArray(array $filesKeys, array $firstFileArr, array $secondFileArr): array
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
