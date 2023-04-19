<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DVar;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DVarTest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDVar
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDirectCallToDVar($expectedResult, $database, $field, $criteria): void
    {
        $result = DVar::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDVar
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDVarAsWorksheetFormula($expectedResult, $database, $field, $criteria): void
    {
        $this->prepareWorksheetWithFormula('DVAR', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDVar(): array
    {
        return [
            [
                8.8,
                self::database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.038433333333,
                self::database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Male'],
                ],
            ],
            [
                0.017433333333,
                self::database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Science', '>8'],
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
