<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DCount;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DCountTest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDCount
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDirectCallToDCount($expectedResult, $database, $field, $criteria): void
    {
        $result = DCount::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDCount
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDCountAsWorksheetFormula($expectedResult, $database, $field, $criteria): void
    {
        $this->prepareWorksheetWithFormula('DCOUNT', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    private function database4(): array
    {
        return [
            ['Status', 'Value'],
            [false, 1],
            [true, 2],
            [true, 4],
            [false, 8],
            [true, 16],
            [false, 32],
            [false, 64],
            [false, 128],
        ];
    }

    public function providerDCount(): array
    {
        return [
            [
                1,
                $this->database1(),
                'Age',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
            [
                1,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Science', 'Male'],
                ],
            ],
            [
                1,
                $this->database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Female'],
                ],
            ],
            [
                3,
                $this->database4(),
                'Value',
                [
                    ['Status'],
                    [true],
                ],
            ],
            [
                5,
                $this->database4(),
                'Value',
                [
                    ['Status'],
                    ['<>true'],
                ],
            ],
            'field column number okay' => [
                0,
                $this->database1(),
                1,
                $this->database1(),
            ],
            'omitted field name' => [
                ExcelError::VALUE(),
                $this->database3(),
                null,
                [
                    ['Subject', 'Score'],
                    ['English', '>63%'],
                ],
            ],
            /* Excel seems to return #NAME? when column number
               is too high or too low. This makes so little sense
               to me that I'm not going to bother coding that up,
               content to return #VALUE! as an invalid name would */
            'field column number too high' => [
                ExcelError::VALUE(),
                $this->database1(),
                99,
                $this->database1(),
            ],
            'field column number too low' => [
                ExcelError::VALUE(),
                $this->database1(),
                0,
                $this->database1(),
            ],
        ];
    }
}
