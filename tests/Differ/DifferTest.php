<?php

namespace Php\Project48\Tests\Differ;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DefferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $firstFile = 'file1.json';
        $secondFile = 'file2.json';
        $result = "- follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true";

        $diff = genDiff($firstFile, $secondFile, null);

        $this->assertEquals($result, $diff);
//        $this->assertEquals(collect($children), $user->getChildren());
    }
}