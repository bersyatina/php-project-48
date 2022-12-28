<?php

namespace Formatters\Stylish;

use function Differ\Differ\isComparisonArray;
use function Differ\Differ\toString;

function getResultToStylish(array $array, int $depth = 1): string
{
    $result = array_map(function ($item) use ($depth, $array) {
        $currentIndent = str_repeat('  ', $depth);
        $longIdent = str_repeat('  ', $depth + 1);
        if (is_array($item)) {
            if (!isComparisonArray($item)) {
                return $longIdent . array_search($item, $array, true) . ": " . getResultToStylish($item, $depth + 2);
            } else {
                $line = "{$currentIndent}{$item['operator']} {$item['key']}: ";
                return is_array($item['value'])
                    ? $line . getResultToStylish($item['value'], $depth + 2)
                    : $line . toString($item['value']);
            }
        }
        return $longIdent . array_search($item, $array, true) . ": " . toString($item);
    }, $array);
    $lastIndent = str_repeat('  ', $depth - 1);
    return "{\n" . implode("\n", $result) . "\n{$lastIndent}}";
}
