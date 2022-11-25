<?php

namespace Formatters;

use function Formatters\Stylish\getResultToStylish;
use function Formatters\Plain\getResultToPlain;

function format($arrResult, $format): string
{
    switch ($format) {
        case "stylish":
            return getResultToStylish($arrResult);
        case "plain":
            return getResultToPlain($arrResult);
        default:
            return 'Неизвестный формат: ' . $format;
    }
}

