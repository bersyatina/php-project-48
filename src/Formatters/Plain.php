<?php

namespace Formatters\Plain;

use function Parsers\Parsers\getDataStatus;
use function Parsers\Parsers\toString;

function getResultToPlain(array $array, string $path = ''): string
{
//    dump($path, $array);
    $result = array_map(function ($item) use (&$result, $array, $path) {
        $path = ltrim($path, ".");
        if (getDataStatus($item)){
            $res = "'{$path}.{$item['key']}' ";
            $filter = array_filter($array, fn($value) => $item['key'] === $value['key']);
            if ($item['operator'] === "+") {
                if (count($filter) !== 2) {
                    $res .= "was added with value: ";
                } else {
                    $res .= "was updated. From ";
                }
                if (is_array($item['value'])) {
                    $res .= "[complex value]";
                } else {
                    $res .= toString($item['value']);
                }

            } elseif ($item['operator'] === "-") {
                $res .= "was removed";
            } elseif ($item['operator'] === " ") {
                if (is_array($item['value'])) {
//                    dump($item['key']);

                    $res = getResultToPlain($item['value'], $path . "." . $item['key']);
                }
            }
            return $res;
        }
    }, $array);

    return implode("\n", $result);
}
