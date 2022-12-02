<?php

namespace Differ\Differ;

use function Parsers\Parsers\decode;
use function Parsers\Parsers\generateKeys;
use function Parsers\Parsers\getResultToArray;
use function Formatters\format;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    $pathToFiles = dirname(__DIR__, 1) . '/files/';

    $firstFileArr = decode($pathToFiles . $firstFile);
    $secondFileArr = decode($pathToFiles . $secondFile);

    $filesKeys = generateKeys($firstFileArr, $secondFileArr);

    $arrResult = getResultToArray($filesKeys, $firstFileArr, $secondFileArr);

    return format($arrResult, $format);
}
