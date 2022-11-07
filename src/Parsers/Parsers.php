<?php

namespace Parsers\Parsers;

use Symfony\Component\Yaml\Yaml;

function decode($pathToFile)
{
    if (!is_file($pathToFile)) {
        return ['Файл не существует'];
    }

    $info = pathinfo($pathToFile);

    switch ($info['extension']) {
        case 'json':
            return json_decode(file_get_contents($pathToFile), true);
        case 'yaml' || 'yml':
            return Yaml::parseFile($pathToFile);
    }
    return ["Файлы с расширением '{$info['extension']}' не поддерживается"];
}
