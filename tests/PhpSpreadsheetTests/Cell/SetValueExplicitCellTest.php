<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use ArgumentCountError;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class SetValueExplicitCellTest extends TestCase
{
    protected string $method = 'setValueExplicit';

    protected int $requiredParameters = 1;

    public function testRequired(): void
    {
        $reflectionMethod = new ReflectionMethod(Cell::class, $this->method);
        $requiredParameters = $reflectionMethod->getNumberOfRequiredParameters();
        self::assertSame($this->requiredParameters, $requiredParameters);
    }

    public static function setValueExplicitTypeArgumentProvider(): array
    {
        return require 'tests/data/Cell/SetValueExplicitTypeArguments.php';
    }

    #[DataProvider('setValueExplicitTypeArgumentProvider')]
    public function testSetValueExplicitTypeArgumentHandling(
        mixed $value,
        ?string $dataType,
        mixed $expectedValue,
        string $expectedDataType
    ): void {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $coordinate = 'A1';
        $cell = $worksheet->getCell($coordinate);

        try {
            if ($dataType !== null) {
                $cell->{$this->method}($value, $dataType);
            } else {
                $cell->{$this->method}($value);
                self::assertSame(1, $this->requiredParameters);
            }
            self::assertSame($expectedValue, $cell->getValue());
            self::assertSame($expectedDataType, $cell->getDataType());
        } catch (ArgumentCountError) {
            self::assertSame(2, $this->requiredParameters);
            self::assertNull($dataType);
        }

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetValueExplicitImage(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        try {
            $sheet->getCell('A1')->setValueExplicit(1, DataType::TYPE_DRAWING_IN_CELL);
            self::fail('Should have thrown exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('not a drawing', $e->getMessage());
        }

        $objDrawing = new Drawing();
        $directory = 'tests/data/Writer/XLSX';
        $objDrawing->setPath($directory . '/blue_square.png');
        $sheet->getCell('C2')->setValueExplicit($objDrawing, DataType::TYPE_DRAWING_IN_CELL);

        $spreadsheet->disconnectWorksheets();
    }
}
