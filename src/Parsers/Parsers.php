<?php

namespace Parsers\Parsers;

use Symfony\Component\Yaml\Yaml;

function decode(string $pathToFile): array
{
    if (is_file($pathToFile)) {
        $file = $pathToFile;
    } elseif (is_file(__DIR__ . "/" . $pathToFile)) {
        $file = __DIR__ . "/" . $pathToFile;
    } else {
        return ["File {$pathToFile} not found!"];
    }

    $pathInfo = pathinfo($pathToFile);
    $extension = array_key_exists('extension', $pathInfo) ? $pathInfo['extension'] : '';

    switch ($extension) {
        case 'json':
            return json_decode((string) file_get_contents($file), true);
        case 'yaml':
        case 'yml':
            return Yaml::parseFile($file);
    }
    return ["Files with the extension '{$extension}' are not supported"];
}
