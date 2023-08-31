<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\TestCase;

class DeprecatedExcelErrorTest extends TestCase
{
    /**
     * @dataProvider providerDeprecatedExcelError
     *
     * @param mixed $expectedResult
     */
    public function testDeprecatedExcelError(callable $deprecatedMethod, $expectedResult): void
    {
        $result = $deprecatedMethod();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDeprecatedExcelError(): array
    {
        return [
            'NULL' => [
                [Functions::class, 'null'],
                ExcelError::null(),
            ],
            'NAN' => [
                [Functions::class, 'NAN'],
                ExcelError::NAN(),
            ],
            'NA' => [
                [Functions::class, 'NA'],
                ExcelError::NA(),
            ],
            'NAME' => [
                [Functions::class, 'NAME'],
                ExcelError::NAME(),
            ],
            'REF' => [
                [Functions::class, 'REF'],
                ExcelError::REF(),
            ],
            'VALUE' => [
                [Functions::class, 'VALUE'],
                ExcelError::VALUE(),
            ],
            'DIV0' => [
                [Functions::class, 'DIV0'],
                ExcelError::DIV0(),
            ],
        ];
    }
}
