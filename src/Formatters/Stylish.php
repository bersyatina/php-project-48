<?php

namespace Formatters\Stylish;

use function Parsers\Parsers\getDataStatus;
use function Parsers\Parsers\toString;

function getResultToStylish(array $array, $depth = 1): string
{
    $result = array_map(function ($item) use (&$result, $depth, $array) {
        $currentIndent = str_repeat('  ', $depth);
        $longIdent = str_repeat('  ', $depth + 1);
        if (is_array($item)) {
            if (!getDataStatus($item)) {
                return $longIdent . array_search($item, $array) . ": " . getResultToStylish($item, $depth + 2);
            } else {
                $line = "{$currentIndent}{$item['operator']} {$item['key']}: ";
                if (is_array($item['value'])) {
                    return $line . getResultToStylish($item['value'], $depth + 2);
                } else {
                    return $line . toString($item['value']);
                }
            }
        }
        $currentIndent = str_repeat('  ', $depth + 1);
        return $currentIndent . array_search($item, $array) . ": " . toString($item);
    }, $array);
    $currentIndent = str_repeat('  ', $depth - 1);
    return "{\n" . implode("\n", $result) . "\n{$currentIndent}}";
}
