<?php

namespace Formatters\Json;

use function Parsers\Parsers\getDataStatus;
use function Parsers\Parsers\toString;

function getJsonData(array $array, string $path = ''): string
{
    $result = array_map(function ($item) use ($array, $path) {
        if (getDataStatus($item)) {
            $path = ltrim($path . '.' . $item['key'], '.');
            $filter = array_filter($array, fn($value) => $item['key'] === $value['key']);
            switch ($item['operator']) {
                case '+':
                    $operation = count($filter) !== 2 ? 'added' : 'replaced';
                    $value = toString($item['value']);
                    break;
                case '-':
                    $operation = count($filter) !== 2 ? 'removed' : 'replaced by';
                    $value = toString($item['value']);
                    break;
                case ' ':
                    $operation = "no changed";
                    $value = is_array($item['value'])
                        ? json_decode(getJsonData($item['value'], $path), true)
                        : toString($item['value']);
            }

            return [
                'path' => $path,
                'operation' => $operation ?? '',
                'value' => $value ?? '',
            ];
        }
    }, $array);

    return json_encode($result, JSON_UNESCAPED_UNICODE);
}

function getResultToJson(array $array): string
{
    return getJsonData($array);
}
