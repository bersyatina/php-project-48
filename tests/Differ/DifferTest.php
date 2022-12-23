<?php

namespace Tests\Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff as genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider differProvider
     *
     * @param string $file1
     * @param string $file2
     * @param string $format
     * @param string $result
     * @return void
     */

    public function testGenDiff($file1, $file2, $result, $format = 'stylish'): void
    {
        $expected = file_get_contents(dirname(__DIR__, 1) . "/fixtures/{$result}");

        $this->assertEquals($expected, genDiff($file1, $file2, $format));
    }

    public function differProvider()
    {
        return [
            ['file3.json', 'file4.json', 'result2'],
            ['file3.yml', 'file4.yaml', 'result2'],
            ['file3.json', 'file4.json', 'result2', 'stylish'],
            ['file3.yml', 'file4.yaml', 'result2', 'stylish'],
            ['file3.json', 'file4.json', 'result3', 'plain'],
            ['file3.yml', 'file4.yaml', 'result3', 'plain'],
            ['file3.json', 'file4.json', 'result4.json', 'json'],
            ['file3.yml', 'file4.yaml', 'result4.json', 'json'],
        ];
    }
}
