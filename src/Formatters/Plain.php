<?php

namespace Formatters\Plain;

use function Parsers\Parsers\isComparisonArray;
use function Parsers\Parsers\toString;

function getPrimitiveData(mixed $data): string
{
    return (is_bool($data) || $data === null || is_numeric($data)) ? toString($data) : "'" . toString($data) . "'";
}

function getPlainData(array $array, string $path = ''): string
{
    $result = array_map(function ($item) use ($array, $path) {
        if (isComparisonArray($item)) {
            $res = "'" . ltrim("{$path}.{$item['key']}", ".") . "'";
            $filter = array_filter($array, fn($value) => $item['key'] === $value['key']);
            switch ($item['operator']) {
                case "+":
                    if (count($filter) !== 2) {
                        return is_array($item['value'])
                            ? "{$res} was added with value: [complex value]"
                            : "{$res} was added with value: " . getPrimitiveData($item['value']);
                    } else {
                        return '';
                    }
                case "-":
                    if (count($filter) !== 2) {
                        return "{$res} was removed";
                    } else {
                        $nextItem = $array[(int) array_search($item, $array, true) + 1];
                        $nextValue = is_array($nextItem['value'])
                            ? "[complex value]"
                            : getPrimitiveData($nextItem['value']);
                        return is_array($item['value'])
                            ? "{$res} was updated. From [complex value] to {$nextValue}"
                            : "{$res} was updated. From " . getPrimitiveData($item['value']) . " to {$nextValue}";
                    }
                case " ":
                    if (is_array($item['value'])) {
                        return getPlainData($item['value'], ltrim($path, ".") . "." . $item['key']);
                    }
            }
        }
        return '';
    }, $array);

    return implode("\nProperty ", array_diff($result, array('')));
}

function getResultToPlain(array $array): string
{
    return "Property " . getPlainData($array);
}
