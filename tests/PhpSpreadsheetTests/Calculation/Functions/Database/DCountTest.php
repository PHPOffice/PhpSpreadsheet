<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DCount;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\Attributes\DataProvider;

class DCountTest extends SetupTeardownDatabases
{
    /**
     * @param mixed[] $database
     * @param mixed[][] $criteria
     */
    #[DataProvider('providerDCount')]
    public function testDirectCallToDCount(int|string $expectedResult, array $database, string|int|null $field, array $criteria): void
    {
        $result = DCount::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @param mixed[] $database
     * @param mixed[][] $criteria
     */
    #[DataProvider('providerDCount')]
    public function testDCountAsWorksheetFormula(int|string $expectedResult, array $database, string|int|null $field, array $criteria): void
    {
        $this->prepareWorksheetWithFormula('DCOUNT', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /** @return array<array{mixed, mixed}> */
    private static function database4(): array
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

    public static function providerDCount(): array
    {
        return [
            [
                1,
                self::database1(),
                'Age',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
            [
                1,
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
                self::database4(),
                'Value',
                [
                    ['Status'],
                    [true],
                ],
            ],
            [
                5,
                self::database4(),
                'Value',
                [
                    ['Status'],
                    ['<>true'],
                ],
            ],
            'field column number okay' => [
                0,
                self::database1(),
                1,
                self::database1(),
            ],
            'omitted field name' => [
                ExcelError::VALUE(),
                self::database3(),
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
