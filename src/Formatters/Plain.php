<?php

namespace Formatters\Plain;

use function Parsers\Parsers\getDataStatus;
use function Parsers\Parsers\toString;

function getPrimitiveData(mixed $data): string
{
    return (is_bool($data) || $data === null || is_numeric($data)) ? toString($data) : "'" . toString($data) . "'";
}

function getPlainData(array $array, string $path = ''): string
{
    $result = array_reduce($array, function ($acc, $item) use ($array, $path) {
        if (getDataStatus($item)) {
            $res = "'" . ltrim("{$path}.{$item['key']}", ".") . "'";
            $filter = array_filter($array, fn($value) => $item['key'] === $value['key']);
            switch ($item['operator']) {
                case "+":
                    if (count($filter) !== 2) {
                        $acc[] = is_array($item['value'])
                            ? "{$res} was added with value: [complex value]"
                            : "{$res} was added with value: " . getPrimitiveData($item['value']);
                    } else {
                        $previousValue = is_array($item['value'])
                            ? "[complex value]"
                            : getPrimitiveData($item['value']);
                        $acc[array_key_last($acc)] .= $previousValue;
                        $acc[] = "";
                    }
                    break;
                case "-":
                    if (count($filter) !== 2) {
                        $acc[] = "{$res} was removed";
                    } else {
                        $acc[] = is_array($item['value'])
                            ? "{$res} was updated. From [complex value] to "
                            : "{$res} was updated. From " . getPrimitiveData($item['value']) . " to ";
                    }
                    break;
                case " ":
                    if (is_array($item['value'])) {
                        $acc[] = getPlainData($item['value'], ltrim($path, ".") . "." . $item['key']);
                    }
            }
        }
        return $acc;
    }, []);

    return implode("\nProperty ", array_diff($result, array('')));
}

function getResultToPlain(array $array): string
{
    return "Property " . getPlainData($array);
}
