<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class UnicodeTest extends AllSetupTeardown
{
    #[DataProvider('providerCODE')]
    public function testCODE(mixed $expectedResult, mixed $character = 'omitted'): void
    {
        // If expected is array, 1st is for CODE, 2nd for UNICODE,
        // 3rd is for Mac CODE if different from Windows.
        if (is_array($expectedResult)) {
            $expectedResult = $expectedResult[1];
        }
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=UNICODE()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=UNICODE(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCODE(): array
    {
        return require 'tests/data/Calculation/TextData/CODE.php';
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerCodeArray')]
    public function testCodeArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=UNICODE({$array})";
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    public static function providerCodeArray(): array
    {
        return [
            'row vector' => [[[80, 72, 80]], '{"P", "H", "P"}'],
            'column vector' => [[[80], [72], [80]], '{"P"; "H"; "P"}'],
            'matrix' => [[[89, 111], [108, 111]], '{"Y", "o"; "l", "o"}'],
        ];
    }
}
