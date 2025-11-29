<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert as CC;
use PHPUnit\Framework\Attributes\DataProvider;

class CodeTest extends AllSetupTeardown
{
    protected function tearDown(): void
    {
        parent::tearDown();
        CC::setWindowsCharacterSet();
    }

    #[DataProvider('providerCODE')]
    public function testCODE(mixed $expectedResult, mixed $character = 'omitted'): void
    {
        // if espected is array, 1st is for code, 2nd for unicode
        if (is_array($expectedResult)) {
            $expectedResult = $expectedResult[0];
        }
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CODE()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CODE(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    #[DataProvider('providerCODE')]
    public function testMacCODE(mixed $expectedResult, mixed $character = 'omitted'): void
    {
        CC::setMacCharacterSet();
        // if espected is array, 1st is for code, 2nd unicode, 3rd Mac CODE
        if (is_array($expectedResult)) {
            $expectedResult = $expectedResult[2] ?? $expectedResult[0];
        }
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CODE()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CODE(A1)');
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

        $formula = "=CODE({$array})";
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
