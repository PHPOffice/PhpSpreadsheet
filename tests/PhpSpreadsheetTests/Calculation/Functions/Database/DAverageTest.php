<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DAverage;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DAverageTest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDAverage
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDirectCallToDAverage($expectedResult, $database, $field, $criteria): void
    {
        $result = DAverage::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDAverage
     *
     * @param mixed $expectedResult
     * @param int|string $field
     */
    public function testDAverageAsWorksheetFormula($expectedResult, array $database, $field, array $criteria): void
    {
        $this->prepareWorksheetWithFormula('DAVERAGE', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDAverage(): array
    {
        return [
            [
                12,
                self::database1(),
                'Yield',
                [
                    ['Tree', 'Height'],
                    ['=Apple', '>10'],
                ],
            ],
            [
                268333.333333333333,
                self::database2(),
                'Sales',
                [
                    ['Quarter', 'Sales Rep.'],
                    ['>1', 'Tina'],
                ],
            ],
            [
                372500,
                self::database2(),
                'Sales',
                [
                    ['Quarter', 'Area'],
                    ['1', 'South'],
                ],
            ],
            'numeric column, in this case referring to age' => [
                13,
                self::database1(),
                3,
                self::database1(),
            ],
            'null field' => [
                ExcelError::VALUE(),
                self::database1(),
                null,
                self::database1(),
            ],
            'field unknown column' => [
                ExcelError::VALUE(),
                self::database1(),
                'xyz',
                self::database1(),
            ],
            'multiple criteria, omit equal sign' => [
                10.5,
                self::database1(),
                'Yield',
                [
                    ['Tree', 'Height'],
                    ['=Apple', '>10'],
                    ['Pear'],
                ],
            ],
            'multiple criteria for same field' => [
                10,
                self::database1(),
                'Yield',
                [
                    ['Tree', 'Height', 'Age', 'Height'],
                    ['=Apple', '>10', null, '<16'],
                ],
            ],
            /* Excel seems to return #NAME? when column number
               is too high or too low. This makes so little sense
               to me that I'm not going to bother coding that up,
               content to return #VALUE! as an invalid name would */
            'field column number too high' => [
                ExcelError::VALUE(),
                self::database1(),
                99,
                self::database1(),
            ],
            'field column number too low' => [
                ExcelError::VALUE(),
                self::database1(),
                0,
                self::database1(),
            ],
        ];
    }
}
