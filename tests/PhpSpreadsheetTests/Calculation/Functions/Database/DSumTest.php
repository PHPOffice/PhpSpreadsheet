<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DSum;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DSumTest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDSum
     */
    public function testDirectCallToDSum(int|float|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $result = DSum::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDSum
     */
    public function testDSumAsWorksheetFormula(int|float|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $this->prepareWorksheetWithFormula('DSUM', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDSum(): array
    {
        return [
            [
                225,
                self::database1(),
                'Profit',
                [
                    ['Tree'],
                    ['=Apple'],
                ],
            ],
            [
                247.8,
                self::database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null],
                ],
            ],
            [
                1210000,
                self::database2(),
                'Sales',
                [
                    ['Quarter', 'Area'],
                    ['>2', 'North'],
                ],
            ],
            [
                710000,
                self::database2(),
                'Sales',
                [
                    ['Quarter', 'Sales Rep.'],
                    ['3', 'C*'],
                ],
            ],
            [
                705000,
                self::database2(),
                'Sales',
                [
                    ['Quarter', 'Sales Rep.'],
                    ['3', '<>C*'],
                ],
            ],
            'omitted field name' => [
                ExcelError::VALUE(),
                self::database1(),
                null,
                self::database1(),
            ],
        ];
    }
}
