<?php

namespace Tests\Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff as genDiff;

class DifferTest extends TestCase
{
//    private $testsData;

//    public function setUp(): void
//    {
//        $this->testsData = [
//            'result2' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result2'),
//            'result3' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result3'),
//            'result4' => file_get_contents(dirname(__DIR__, 1) . '/fixtures/result4.json'),
//            'diffJson' => genDiff('file3.json', 'file4.json'),
//            'diffYaml' => genDiff('file3.yml', 'file4.yaml'),
//            'diffJsonStylish' => genDiff('file3.json', 'file4.json', 'stylish'),
//            'diffYamlStylish' => genDiff('file3.yml', 'file4.yaml', 'stylish'),
//            'diffJsonPlain' => genDiff('file3.json', 'file4.json', 'plain'),
//            'diffYamlPlain' => genDiff('file3.yml', 'file4.yaml', 'plain'),
//            'diffJsonJson' => genDiff('file3.json', 'file4.json', 'json'),
//            'diffYamlJson' => genDiff('file3.yml', 'file4.yaml', 'json'),
//        ];
//    }

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

//    public function testGenDiffStylish()
//    {
//        $this->assertEquals($this->testsData['result2'], $this->testsData['diffJsonStylish']);
//        $this->assertEquals($this->testsData['result2'], $this->testsData['diffYamlStylish']);
//    }
//
//    public function testGenDiffPlain()
//    {
//        $this->assertEquals($this->testsData['result3'], $this->testsData['diffJsonPlain']);
//        $this->assertEquals($this->testsData['result3'], $this->testsData['diffYamlPlain']);
//    }
//
//    public function testGenDiffJson()
//    {
//        $this->assertEquals($this->testsData['result4'], $this->testsData['diffJsonJson']);
//        $this->assertEquals($this->testsData['result4'], $this->testsData['diffYamlJson']);
//    }
}
