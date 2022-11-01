<?php

namespace Tests\Differ;

use function Differ\Differ\genDiff as genDiff;
use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $result = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result');
        $firstFile = 'file1.json';
        $secondFile = 'file2.json';

        $diff = genDiff($firstFile, $secondFile, 'json');

        $this->assertEquals($diff, $result);
    }

    public function testGenDiffYamlFiles(): void
    {
        $result1 = file_get_contents(dirname(__DIR__, 1) . '/fixtures/files/result');
        $firstFile = 'file1.yml';
        $secondFile = 'file2.yaml';

        $diff = genDiff($firstFile, $secondFile, 'yaml');
        $this->assertEquals($diff, $result1);
    }
}