<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\ExcelFunctions;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PHPUnit\Framework\TestCase;

class ExcelFunctionsCollectionTest extends TestCase
{
    public function testRecognisedExcelFunction(): void
    {
        $excelFunctionCollection = new ExcelFunctions();

        self::assertFalse($excelFunctionCollection->isRecognisedExcelFunction('AAA'));
        self::assertTrue($excelFunctionCollection->isRecognisedExcelFunction('ABS'));
    }

    public function testFunctionDefinitionLoadsAsArrayElement(): void
    {
        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionDefinition = $excelFunctionCollection['ABS'];

        self::assertInstanceOf(XlFunctionAbstract::class, $excelFunctionDefinition);
    }

    public function testReadOnlyArrayAccess(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionCollection['ABS']; // @phpstan-ignore-line
        $excelFunctionCollection['ABS'] = 'NO LONGER ABS';
    }

    public function testUnsetBlockedAsArray(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionCollection['ABS']; // @phpstan-ignore-line
        unset($excelFunctionCollection['ABS']);
    }

    public function testFunctionDefinitionLoadsAsObjectProperty(): void
    {
        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionDefinition = $excelFunctionCollection->ABS; // @phpstan-ignore-line
        self::assertInstanceOf(XlFunctionAbstract::class, $excelFunctionDefinition);
    }

    public function testReadOnlyObjectAccess(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionCollection->ABS = 'NO LONGER ABS'; // @phpstan-ignore-line
    }

    public function testUnsetBlockedAsObjectProperty(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        unset($excelFunctionCollection->ABS);
    }
}
