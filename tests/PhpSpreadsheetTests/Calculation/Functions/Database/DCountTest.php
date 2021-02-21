<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DCount;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DCountTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDCount
     *
     * @param mixed $expectedResult
     */
    public function testDCount($expectedResult, $database, $field, $criteria)
    {
        $result = DCount::evaluate($database, $field, $criteria);
        self::assertSame($expectedResult, $result);
    }

    protected function database()
    {
        return [
            ['Tree', 'Height', 'Age', 'Yield', 'Profit'],
            ['Apple', 18, 20, 14, 105],
            ['Pear', 12, 12, 10, 96],
            ['Cherry', 13, 14, 9, 105],
            ['Apple', 14, 'N/A', 10, 75],
            ['Pear', 9, 8, 8, 77],
            ['Apple', 12, 11, 6, 45],
        ];
    }

    public function providerDCount()
    {
        return [
            [
                1,
                $this->database(),
                'Age',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
        ];
    }
}
