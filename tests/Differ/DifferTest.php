<?php

namespace Tests\Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff as genDiff;

class DifferTest extends TestCase
{
    private $testsData;

    public function setUp(): void
    {
        $this->testsData = [
            'result1' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result'),
            'result2' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result2'),
            'result3' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result3'),
            'result4' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result4.json'),
            'firstFileJson' => 'file1.json',
            'twoFileJson' => 'file2.json',
            'treeFileJson' => 'file3.json',
            'fourFileJson' => 'file4.json',
            'firstFileYml' => 'file1.yml',
            'twoFileYml' => 'file2.yaml',
            'treeFileYml' => 'file3.yml',
            'fourFileYml' => 'file4.yaml',
        ];
    }

    public function testGenDiffDefault(): void
    {
        $diff1 = genDiff($this->testsData['firstFileJson'], $this->testsData['twoFileJson']);
        $this->assertEquals($this->testsData['result1'], $diff1);

        $diff2 = genDiff($this->testsData['firstFileYml'], $this->testsData['twoFileYml']);
        $this->assertEquals($this->testsData['result1'], $diff2);
    }

    public function testGenDiffStylish()
    {
        $diff3 = genDiff($this->testsData['treeFileJson'], $this->testsData['fourFileJson'], 'stylish');
        $this->assertEquals($this->testsData['result2'], $diff3);

        $diff4 = genDiff($this->testsData['treeFileYml'], $this->testsData['fourFileYml'], 'stylish');
        $this->assertEquals($this->testsData['result2'], $diff4);
    }

    public function testGenDiffPlain()
    {
        $diff5 = genDiff($this->testsData['treeFileJson'], $this->testsData['fourFileJson'], 'plain');
        $this->assertEquals($this->testsData['result3'], $diff5);

        $diff6 = genDiff($this->testsData['treeFileYml'], $this->testsData['fourFileYml'], 'plain');
        $this->assertEquals($this->testsData['result3'], $diff6);
    }

    public function testGenDiffJson()
    {
        $diff7 = genDiff($this->testsData['treeFileJson'], $this->testsData['fourFileJson'], 'json');
        $this->assertEquals($this->testsData['result4'], $diff7);

        $diff8 = genDiff($this->testsData['treeFileYml'], $this->testsData['fourFileYml'], 'json');
        $this->assertEquals($this->testsData['result4'], $diff8);
    }
}
