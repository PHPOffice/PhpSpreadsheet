<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

class DayTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDAY
     *
     * @param mixed $expectedResultExcel
     */
    public function testDAY($expectedResultExcel, string $dateTimeValue): void
    {
        $this->mightHaveException($expectedResultExcel);
        $sheet = $this->getSheet();
        $sheet->getCell('B1')->setValue('1954-11-23');
        $sheet->getCell('A1')->setValue("=DAY($dateTimeValue)");
        self::assertSame($expectedResultExcel, $sheet->getCell('A1')->getCalculatedValue());
    }

    public function providerDAY(): array
    {
        return require 'tests/data/Calculation/DateTime/DAY.php';
    }

    /**
     * @dataProvider providerDAYOpenOffice
     *
     * @param mixed $expectedResultOpenOffice
     */
    public function testDAYOpenOffice($expectedResultOpenOffice, string $dateTimeValue): void
    {
        self::setOpenOffice();
        $this->mightHaveException($expectedResultOpenOffice);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue("=DAY($dateTimeValue)");
        self::assertSame($expectedResultOpenOffice, $sheet->getCell('A2')->getCalculatedValue());
    }

    public function providerDAYOpenOffice(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYOpenOffice.php';
    }
}
