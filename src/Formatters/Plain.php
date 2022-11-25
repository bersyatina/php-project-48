<?php

namespace Formatters\Plain;

use function Parsers\Parsers\getDataStatus;
use function Parsers\Parsers\toString;

function getResultToPlain(array $array, string $path = ''): string
{
//    dump($path, $array);
    $result = array_map(function ($item) use (&$result, $array, $path) {
        if (getDataStatus($item)){
            $res = "'{$item['key']}' ";
            if ($item['operator'] === "+") {
                $res .= "was added with value: ";
                if (is_array($item['value'])) {
                    $res .= "[complex value]";
                } else {
                    $res .= $item['value'];
                }
            } elseif ($item['operator'] === "-") {
                $res .= "was removed";
            } elseif ($item['operator'] === " ") {
                $res = "";
            }
            return $res;
        }

    }, $array);

    return "Property " . implode("\nProperty ", $result);
}
