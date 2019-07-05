<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StatisticalTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMAXIFS
     */
    public function testMAXIFS($expectedResult, ...$args)
    {
        $result = Statistical::MAXIFS(...$args);
        self::assertEquals('#Not Yet Implemented', $result);
    }

    public function providerMAXIFS()
    {
        return require 'data/Calculation/Statistical/MAXIFS.php';
    }

    /**
     * @dataProvider providerMINIFS
     */
    public function testMINIFS($expectedResult, ...$args)
    {
        $result = Statistical::MINIFS(...$args);
        self::assertEquals('#Not Yet Implemented', $result);
    }

    public function providerMINIFS()
    {
        return require 'data/Calculation/Statistical/MINIFS.php';
    }
}
