<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DCountA;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class DCountATest extends SetupTeardownDatabases
{
    /**
     * @dataProvider providerDCountA
     */
    public function testDirectCallToDCountA(int|string $expectedResult, array $database, string $field, array $criteria): void
    {
        $result = DCountA::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @dataProvider providerDCountA
     */
    public function testDCountAAsWorksheetFormula(int|string $expectedResult, array $database, string $field, array $criteria): void
    {
        $this->prepareWorksheetWithFormula('DCOUNTA', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDCountA(): array
    {
        return [
            [
                1,
                self::database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
            [
                2,
                self::database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Science', 'Male'],
                ],
            ],
            [
                1,
                self::database3(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Female'],
                ],
            ],
            [
                3,
                self::database3(),
                'Score',
                [
                    ['Subject', 'Score'],
                    ['English', '>60%'],
                ],
            ],
            'invalid field name' => [
                ExcelError::VALUE(),
                self::database3(),
                'Scorex',
                [
                    ['Subject', 'Score'],
                    ['English', '>60%'],
                ],
            ],
        ];
    }
}
