<?php

namespace Tests\Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff as genDiff;

class DifferTest extends TestCase
{
    public function testGenDiffFiles(): void
    {
        $result1 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/result');
        $result2 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/result2');
        $result3 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/result3');
        $result4 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/result4.json');

        $firstFileJson = 'file1.json';
        $twoFileJson = 'file2.json';

        $treeFileJson = 'file3.json';
        $fourFileJson = 'file4.json';

        $firstFileYml = 'file1.yml';
        $twoFileYml = 'file2.yaml';

        $treeFileYml = 'file3.yml';
        $fourFileYml = 'file4.yaml';

        $diff1 = genDiff($firstFileJson, $twoFileJson, 'stylish');
        $this->assertEquals($result1, $diff1);

        $diff2 = genDiff($firstFileYml, $twoFileYml, 'stylish');
        $this->assertEquals($result1, $diff2);

        $diff3 = genDiff($treeFileJson, $fourFileJson, 'stylish');
        $this->assertEquals($result2, $diff3);

        $diff4 = genDiff($treeFileYml, $fourFileYml, 'stylish');
        $this->assertEquals($result2, $diff4);

        $diff5 = genDiff($treeFileJson, $fourFileJson, 'plain');
        $this->assertEquals($result3, $diff5);

        $diff6 = genDiff($treeFileYml, $fourFileYml, 'plain');
        $this->assertEquals($result3, $diff6);

        $diff7 = genDiff($treeFileJson, $fourFileJson, 'json');
        $this->assertEquals($result4, $diff7);

        $diff8 = genDiff($treeFileYml, $fourFileYml, 'json');
        $this->assertEquals($result4, $diff8);
    }
}
