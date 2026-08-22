<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert as CC;
use PHPUnit\Framework\Attributes\DataProvider;

class CharTest extends AllSetupTeardown
{
    protected function tearDown(): void
    {
        parent::tearDown();
        CC::setWindowsCharacterSet();
    }

    #[DataProvider('providerCHAR')]
    public function testCHAR(mixed $expectedResult, mixed $character = 'omitted'): void
    {
        // If expected is array, 1st is for CHAR, 2nd for UNICHAR,
        // 3rd is for Mac CHAR if different from Windows.
        if (is_array($expectedResult)) {
            $expectedResult = $expectedResult[0];
        }
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CHAR()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CHAR(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    #[DataProvider('providerCHAR')]
    public function testMacCHAR(mixed $expectedResult, mixed $character = 'omitted'): void
    {
        CC::setMacCharacterSet();
        // If expected is array, 1st is for CHAR, 2nd for UNICHAR,
        // 3rd is for Mac CHAR if different from Windows.
        if (is_array($expectedResult)) {
            $expectedResult = $expectedResult[2] ?? $expectedResult[0];
        }
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($character === 'omitted') {
            $sheet->getCell('B1')->setValue('=CHAR()');
        } else {
            $this->setCell('A1', $character);
            $sheet->getCell('B1')->setValue('=CHAR(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCHAR(): array
    {
        return require 'tests/data/Calculation/TextData/CHAR.php';
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerCharArray')]
    public function testCharArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CHAR({$array})";
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    public static function providerCharArray(): array
    {
        return [
            'row vector' => [[['P', 'H', 'P']], '{80, 72, 80}'],
            'column vector' => [[['P'], ['H'], ['P']], '{80; 72; 80}'],
            'matrix' => [[['Y', 'o'], ['l', 'o']], '{89, 111; 108, 111}'],
        ];
    }
}
