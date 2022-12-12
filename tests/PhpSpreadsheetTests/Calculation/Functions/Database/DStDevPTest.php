<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DStDevP;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DStDevPTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDStDevP
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDirectCallToDStDevP($expectedResult, $database, $field, $criteria): void
    {
        $result = DStDevP::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDStDevP
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDStDevPAsWorksheetFormula($expectedResult, $database, $field, $criteria): void
    {
        $this->prepareWorksheetWithFormula('DSTDEVP', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public function providerDStDevP(): array
    {
        return [
            [
                2.653299832284,
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.085244745684,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['English', 'Male'],
                ],
            ],
            [
                0.160623784042,
                $this->database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Math', '>8'],
                ],
            ],
            [
                0.01,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['English', 'Male'],
                ],
            ],
            [
                0,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Math', '>8'],
                ],
            ],
            'omitted field name' => [
                ExcelError::VALUE(),
                $this->database1(),
                null,
                $this->database1(),
            ],
        ];
    }
}
