<?php

namespace Differ\Differ;

use function Parsers\Parsers\decode;
use function Formatters\stylish\generateKeys;
use function Formatters\stylish\getResultToArray;
use function Formatters\stylish\getResultToString;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    $pathToFiles = dirname(__DIR__, 1) . '/files/';

    $firstFileArr = decode($pathToFiles . $firstFile);
    $secondFileArr = decode($pathToFiles . $secondFile);

    $filesKeys = generateKeys($firstFileArr, $secondFileArr);

    $arrResult = getResultToArray($filesKeys, $firstFileArr, $secondFileArr);

    return format($arrResult, $format);
}

function format($arrResult, $format): string
{
    switch ($format) {
        case "stylish":
            return getResultToString($arrResult);
        default:
            return 'Неизвестный формат: ' . $format;
    }
}
