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
        $firstFile = 'file1.json';
        $secondFile = 'file2.json';
        $treeFile = 'file3.json';
        $fourFile = 'file4.json';

        $diff1 = genDiff($firstFile, $secondFile, 'json');
        $this->assertEquals($result1, $diff1);
        $diff2 = genDiff($treeFile, $fourFile, 'json');
        $this->assertEquals($result2, $diff2);
    }

    public function testGenDiffYamlFiles(): void
    {
        $result1 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result');
        $result2 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result2');

        $firstFile = 'file1.yml';
        $secondFile = 'file2.yaml';
        $treeFile = 'file3.yml';
        $fourFile = 'file4.yaml';


        $diff1 = genDiff($firstFile, $secondFile, 'yaml');
        $this->assertEquals($diff1, $result1);
        $diff2 = genDiff($treeFile, $fourFile, 'yaml');
        $this->assertEquals($diff2, $result2);
    }
}