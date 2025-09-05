<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\ExceptionHandler;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

class CalculationCoverageTest extends TestCase
{
    public function testClone(): void
    {
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage('Cloning the calculation engine is not allowed!');
        $calc = Calculation::getInstance();
        $clone = clone $calc;
        $clone->flushInstance();
    }

    public function testBadInstanceArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $calc = Calculation::getInstance($spreadsheet);
        $type = $calc->getInstanceArrayReturnType();
        self::assertFalse($calc->setInstanceArrayReturnType('bad'));
        self::assertSame($type, $calc->getInstanceArrayReturnType());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCalculate(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calc = Calculation::getInstance($spreadsheet);
        $sheet->getCell('A1')->setValue('=2+3');
        $result = $calc->calculate($sheet->getCell('A1'));
        self::assertSame(5, $result);
        self::assertSame('', Calculation::boolToString(null));
        $spreadsheet->disconnectWorksheets();
    }

    public function testCalculateBad(): void
    {
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage('Formula Error');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calc = Calculation::getInstance($spreadsheet);
        $sheet->getCell('A1')->setValue('=SUM(');
        $result = $calc->calculate($sheet->getCell('A1'));
        self::assertSame(5, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public function testParse(): void
    {
        $calc = Calculation::getInstance();
        self::assertSame([], $calc->parseFormula('2+3'), 'no leading =');
        self::assertSame([], $calc->parseFormula('='), 'leading = but no other text');
    }

    public function testExtractNamedRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $calc = Calculation::getInstance($spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('mysheet');
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B1', 2);
        $sheet->setCellValue('A2', 3);
        $sheet->setCellValue('B2', 4);
        $spreadsheet->addNamedRange(
            new NamedRange('Whatever', $sheet, '$A$1:$B$2')
        );
        $range = 'Whatever';
        $result = $calc->extractNamedRange($range, $sheet);
        self::assertSame('$A$1:$B$2', $range);
        self::assertSame([1 => ['A' => 1, 'B' => 2], 2 => ['A' => 3, 'B' => 4]], $result);
        $range = 'mysheet!Whatever';
        $result = $calc->extractNamedRange($range, $sheet);
        self::assertSame('$A$1:$B$2', $range);
        self::assertSame([1 => ['A' => 1, 'B' => 2], 2 => ['A' => 3, 'B' => 4]], $result);

        $range = 'mysheet!Whateverx';
        $result = $calc->extractNamedRange($range, $sheet);
        self::assertSame('Whateverx', $range);
        self::assertSame('#REF!', $result);

        $range = 'Why';
        $result = $calc->extractNamedRange($range, $sheet);
        self::assertSame('Why', $range);
        self::assertSame('#REF!', $result);

        $spreadsheet->addNamedRange(
            new NamedRange('OneCell', $sheet, '$A$1')
        );
        $range = 'OneCell';
        $result = $calc->extractNamedRange($range, $sheet);
        self::assertSame('$A$1', $range);
        self::assertSame([1 => ['A' => 1]], $result);

        $spreadsheet->addNamedRange(
            new NamedRange('NoSuchCell', $sheet, '$Z$1')
        );
        $range = 'NoSuchCell';
        $result = $calc->extractNamedRange($range, $sheet);
        self::assertSame('$Z$1', $range);
        self::assertSame([1 => ['Z' => null]], $result);

        $spreadsheet->addNamedRange(
            new NamedRange('SomeCells', $sheet, '$B$1:$C$2')
        );
        $range = 'SomeCells';
        $result = $calc->extractNamedRange($range, $sheet);
        self::assertSame('$B$1:$C$2', $range);
        self::assertSame([1 => ['B' => 2, 'C' => null], 2 => ['B' => 4, 'C' => null]], $result);

        $spreadsheet->disconnectWorksheets();
    }

    protected static int $winMinPhpToSkip = 80300;

    protected static int $winMaxPhpToSkip = 80499;

    protected static string $winIndicator = 'WIN';

    // separate process because it sets its own handler
    #[Attributes\RunInSeparateProcess]
    public function testExceptionHandler(): void
    {
        if (
            strtoupper(substr(PHP_OS, 0, 3)) === self::$winIndicator
            && PHP_VERSION_ID >= self::$winMinPhpToSkip
            && PHP_VERSION_ID <= self::$winMaxPhpToSkip
        ) {
            self::markTestSkipped('Mysterious problem on Windows with Php8.3/4 only');
        }
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage('hello');
        $handler = new ExceptionHandler();
        trigger_error('hello');
        self::assertNotNull($handler); // @phpstan-ignore-line
    }
}
