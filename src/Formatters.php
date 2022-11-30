<?php

namespace Formatters;

use function Formatters\Stylish\getResultToStylish;
use function Formatters\Plain\getResultToPlain;
use function Formatters\Json\getResultToJson;

function format($arrResult, $format): string
{
    switch ($format) {
        case "stylish":
            return getResultToStylish($arrResult);
        case "plain":
            return getResultToPlain($arrResult);
        case "json":
            return getResultToJson($arrResult);
        default:
            return 'The format is not defined: ' . $format;
    }
}
