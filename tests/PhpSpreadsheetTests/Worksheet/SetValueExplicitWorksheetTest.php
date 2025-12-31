<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use ArgumentCountError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class SetValueExplicitWorksheetTest extends TestCase
{
    protected string $method = 'setCellValueExplicit';

    protected int $requiredParameters = 3;

    public function testRequired(): void
    {
        $reflectionMethod = new ReflectionMethod(Worksheet::class, $this->method);
        $requiredParameters = $reflectionMethod->getNumberOfRequiredParameters();
        self::assertSame($this->requiredParameters, $requiredParameters);
    }

    public static function setCellValueExplicitTypeArgumentProvider(): array
    {
        return require 'tests/data/Cell/SetValueExplicitTypeArguments.php';
    }

    #[DataProvider('setCellValueExplicitTypeArgumentProvider')]
    public function testSetCellValueExplicitTypeArgumentHandling(
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
            if ($dataType) {
                $worksheet->{$this->method}($coordinate, $value, $dataType);
            } else {
                $worksheet->{$this->method}($coordinate, $value);
                self::assertSame(2, $this->requiredParameters);
            }
            self::assertSame($expectedValue, $cell->getValue());
            self::assertSame($expectedDataType, $cell->getDataType());
        } catch (ArgumentCountError) {
            self::assertSame(3, $this->requiredParameters);
            self::assertNull($dataType);
        }

        $spreadsheet->disconnectWorksheets();
    }
}
