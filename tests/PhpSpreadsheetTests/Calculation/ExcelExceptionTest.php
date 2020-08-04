<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PHPUnit\Framework\TestCase;

class ExcelExceptionTest extends TestCase
{
    /**
     * @dataProvider providerExcelException
     *
     * @param string $name
     * @param ExcelException $expectedResult
     */
    public function testFromErrorName($name, $expectedResult)
    {
        $result = ExcelException::fromErrorName($name);

        self::assertEquals($expectedResult, $result);
    }

    public function providerExcelException()
    {
        return require 'tests/data/Calculation/ExcelException.php';
    }

    public function testFromInvalidErrorName()
    {
        self:$this->expectException(SpreadsheetException::class);
        $result = ExcelException::fromErrorName('#MARK!');
    }
}
