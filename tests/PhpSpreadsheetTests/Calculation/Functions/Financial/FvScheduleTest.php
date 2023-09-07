<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class FvScheduleTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFVSCHEDULE
     */
    public function testFVSCHEDULE(mixed $expectedResult, mixed $principal = null, ?array $schedule = null): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = '=FVSCHEDULE(';
        if ($principal !== null) {
            $this->setCell('A1', $principal);
            $formula .= 'A1';
            if (!empty($schedule)) {
                $row = 0;
                foreach ($schedule as $value) {
                    ++$row;
                    $this->setCell("B$row", $value);
                }
                $formula .= ",B1:B$row";
            }
        }
        $formula .= ')';
        $sheet->getCell('D1')->setValue($formula);
        $result = $sheet->getCell('D1')->getCalculatedValue();
        $this->adjustResult($result, $expectedResult);

        self::assertEqualsWithDelta($expectedResult, $result, 1.0E-8);
    }

    public static function providerFVSCHEDULE(): array
    {
        return require 'tests/data/Calculation/Financial/FVSCHEDULE.php';
    }
}
