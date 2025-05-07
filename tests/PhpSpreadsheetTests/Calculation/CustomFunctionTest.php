<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\TestCase;

class CustomFunctionTest extends TestCase
{
    public static function testCustomFunction(): void
    {
        $calculation = Calculation::getInstance();
        $key = 'FOURTHPOWER';
        $value = [
            'category' => 'custom',
            'functionCall' => [CustomFunction::class, 'fourthPower'],
            'argumentCount' => '1',
        ];
        self::assertTrue(Calculation::addFunction($key, $value));
        self::assertFalse(Calculation::addFunction('sqrt', $value));
        self::assertSame(16, $calculation->calculateFormula('=FOURTHPOWER(2)'));
        self::assertSame('#VALUE!', $calculation->calculateFormula('=FOURTHPOWER("X")'));
        self::assertSame('#NAME?', $calculation->calculateFormula('=FOURTHPOWE("X")'));
        self::assertFalse(Calculation::removeFunction('SQRT'));
        self::assertSame(2.0, $calculation->calculateFormula('=SQRT(4)'));
        self::assertFalse(Calculation::removeFunction('sqrt'));
        self::assertSame(4.0, $calculation->calculateFormula('=sqrt(16)'));
        self::assertTrue(Calculation::removeFunction($key));
        self::assertSame('#NAME?', $calculation->calculateFormula('=FOURTHPOWER(3)'));
        self::assertFalse(Calculation::removeFunction('WHATEVER'));
    }
}
