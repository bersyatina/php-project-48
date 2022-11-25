<?php

namespace Formatters\Plain;

use function Parsers\Parsers\getDataStatus;
use function Parsers\Parsers\toString;

function getPrimitiveData(mixed $data): mixed
{
    if (is_bool($data) || $data === null) {
        return toString($data);
    } else {
        return "'" . toString($data). "'";
    }
}

function getPlainData(array $array, string $path = ''): string
{
    $result = array_reduce($array, function ($acc, $item) use (&$result, $array, $path) {
        $res = "";
        if (getDataStatus($item)){
            $res = ltrim("{$path}.{$item['key']}", ".");
            $res = "'{$res}'";
            $filter = array_filter($array, fn($value) => $item['key'] === $value['key']);
            if ($item['operator'] === "+") {
                if (count($filter) !== 2) {
                    if (is_array($item['value'])) {
                        $acc[] = "{$res} was added with value: [complex value]";
                    } else {
                        $acc[] = "{$res} was added with value: " . getPrimitiveData($item['value']);
                    }
                } else {
                    $acc[array_key_last($acc)] .= getPrimitiveData($item['value']);
                    $acc[] = "";
                }
            } elseif ($item['operator'] === "-") {
                if (count($filter) !== 2) {
                    $acc[] = "{$res} was removed";
                } else {
                    if (is_array($item['value'])) {
                        $acc[] = "{$res} was updated. From [complex value] to ";
                    } else {
                        $acc[] = "{$res} was updated. From " . getPrimitiveData($item['value']) . " to ";
                    }
                }
            } elseif ($item['operator'] === " ") {
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