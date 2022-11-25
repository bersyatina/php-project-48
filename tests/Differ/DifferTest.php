<?php

namespace Tests\Differ;

use function Differ\Differ\genDiff as genDiff;
use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    public function testGenDiffJsonFiles(): void
    {
        $result1 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result');
        $result2 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result2');
        $result3 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result3');

        $firstFile = 'file1.json';
        $secondFile = 'file2.json';

        $treeFile = 'file3.json';
        $fourFile = 'file4.json';

        $diff1 = genDiff($firstFile, $secondFile, 'stylish');
        $this->assertEquals($result1, $diff1);

        $diff2 = genDiff($treeFile, $fourFile, 'stylish');
        $this->assertEquals($result2, $diff2);

        $diff3 = genDiff($treeFile, $fourFile, 'plain');
        $this->assertEquals($result3, $diff3);
    }

    public function testGenDiffYamlFiles(): void
    {
        $result1 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result');
        $result2 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result2');
        $result3 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result3');

        $firstFile = 'file1.yml';
        $secondFile = 'file2.yaml';

        $treeFile = 'file3.yml';
        $fourFile = 'file4.yaml';

        $diff1 = genDiff($firstFile, $secondFile, 'stylish');
        $this->assertEquals($diff1, $result1);

        $diff2 = genDiff($treeFile, $fourFile, 'stylish');
        $this->assertEquals($diff2, $result2);

        $diff3 = genDiff($treeFile, $fourFile, 'plain');
        $this->assertEquals($diff3, $result3);
    }
}