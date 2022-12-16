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
            'result2' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result2'),
            'result3' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result3'),
            'result4' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result4.json'),
            'diffJson' => genDiff('file3.json', 'file4.json'),
            'diffYaml' => genDiff('file3.yml', 'file4.yaml'),
            'diffJsonStylish' => genDiff('file3.json', 'file4.json', 'stylish'),
            'diffYamlStylish' => genDiff('file3.yml', 'file4.yaml', 'stylish'),
            'diffJsonPlain' => genDiff('file3.json', 'file4.json', 'plain'),
            'diffYamlPlain' => genDiff('file3.yml', 'file4.yaml', 'plain'),
            'diffJsonJson' => genDiff('file3.json', 'file4.json', 'json'),
            'diffYamlJson' => genDiff('file3.yml', 'file4.yaml', 'json'),
        ];
    }

    public function testGenDiffDefault(): void
    {
        $this->assertEquals($this->testsData['result2'], $this->testsData['diffJson']);
        $this->assertEquals($this->testsData['result2'], $this->testsData['diffYaml']);
    }

    public function testGenDiffStylish()
    {
        $this->assertEquals($this->testsData['result2'], $this->testsData['diffJsonStylish']);
        $this->assertEquals($this->testsData['result2'], $this->testsData['diffYamlStylish']);
    }

    public function testGenDiffPlain()
    {
        $this->assertEquals($this->testsData['result3'], $this->testsData['diffJsonPlain']);
        $this->assertEquals($this->testsData['result3'], $this->testsData['diffYamlPlain']);
    }

    public function testGenDiffJson()
    {
        $this->assertEquals($this->testsData['result4'], $this->testsData['diffJsonJson']);
        $this->assertEquals($this->testsData['result4'], $this->testsData['diffYamlJson']);
    }
}
