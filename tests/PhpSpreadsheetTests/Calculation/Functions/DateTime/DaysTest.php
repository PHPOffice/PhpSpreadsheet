<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTime;
use DateTimeImmutable;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;

class DaysTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDAYS
     *
     * @param mixed $expectedResult
     */
    public function testDAYS($expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('C1')->setValue('1954-11-30');
        $sheet->getCell('A1')->setValue("=DAYS($formula)");
        self::assertSame($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDAYS(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYS.php';
    }

    public function testObject(): void
    {
        $obj1 = new DateTime('2000-3-31');
        $obj2 = new DateTimeImmutable('2000-2-29');
        self::assertSame(31, Days::between($obj1, $obj2));
    }

    public function testNonDateObject(): void
    {
        $obj1 = new Exception();
        $obj2 = new DateTimeImmutable('2000-2-29');
        self::assertSame('#VALUE!', Days::between($obj1, $obj2));
    }
}
