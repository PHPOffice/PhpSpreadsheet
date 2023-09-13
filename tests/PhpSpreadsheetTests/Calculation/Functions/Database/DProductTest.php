<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DProduct;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateTimeHelper;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DProductTest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDProduct
     */
    public function testDirectCallToDProduct(float|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $result = DProduct::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDProduct
     */
    public function testDProductAsWorksheetFormula(float|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $this->prepareWorksheetWithFormula('DPRODUCT', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    private static function database5(): array
    {
        return [
            ['Name', 'Date', 'Test', 'Score'],
            ['Gary', DateTimeHelper::getDateValue('01-Jan-2017'), 'Test1', 4],
            ['Gary', DateTimeHelper::getDateValue('01-Jan-2017'), 'Test2', 4],
            ['Gary', DateTimeHelper::getDateValue('01-Jan-2017'), 'Test3', 3],
            ['Gary', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test1', 3],
            ['Gary', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test2', 4],
            ['Gary', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test3', 3],
            ['Kev', DateTimeHelper::getDateValue('02-Jan-2017'), 'Test1', 2],
            ['Kev', DateTimeHelper::getDateValue('02-Jan-2017'), 'Test2', 3],
            ['Kev', DateTimeHelper::getDateValue('02-Jan-2017'), 'Test3', 5],
            ['Kev', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test1', 3],
            ['Kev', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test2', 2],
            ['Kev', DateTimeHelper::getDateValue('05-Jan-2017'), 'Test3', 5],
        ];
    }

    public static function providerDProduct(): array
    {
        return [
            [
                800.0,
                self::database1(),
                'Yield',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', null, null],
                ],
            ],
            [
                36.0,
                self::database5(),
                'Score',
                [
                    ['Name', 'Date'],
                    ['Gary', '05-Jan-2017'],
                ],
            ],
            [
                8.0,
                self::database5(),
                'Score',
                [
                    ['Test', 'Date'],
                    ['Test1', '<05-Jan-2017'],
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
