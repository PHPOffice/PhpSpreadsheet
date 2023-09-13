<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DStDevP;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DStDevPTest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDStDevP
     */
    public function testDirectCallToDStDevP(float|int|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $result = DStDevP::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDStDevP
     */
    public function testDStDevPAsWorksheetFormula(float|int|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $this->prepareWorksheetWithFormula('DSTDEVP', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDStDevP(): array
    {
        return [
            [
                2.653299832284,
                self::database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.085244745684,
                self::database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['English', 'Male'],
                ],
            ],
            [
                0.160623784042,
                self::database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Math', '>8'],
                ],
            ],
            [
                0.01,
                self::database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['English', 'Male'],
                ],
            ],
            [
                0,
                self::database3(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Math', '>8'],
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
