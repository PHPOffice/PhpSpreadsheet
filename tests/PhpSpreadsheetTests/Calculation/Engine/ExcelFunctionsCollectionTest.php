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
        self::assertSame('ABS', $excelFunctionDefinition->name);
    }

    public function testReadOnlyArrayAccess(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionDefinition = $excelFunctionCollection['ABS']; // @phpstan-ignore-line
        self::assertInstanceOf(XlFunctionAbstract::class, $excelFunctionDefinition);
        self::assertSame('ABS', $excelFunctionDefinition->name);

        $excelFunctionCollection['ABS'] = 'NO LONGER ABS';
    }

    public function testUnsetBlockedAsArray(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionDefinition = $excelFunctionCollection['ABS']; // @phpstan-ignore-line
        self::assertInstanceOf(XlFunctionAbstract::class, $excelFunctionDefinition);
        self::assertSame('ABS', $excelFunctionDefinition->name);

        unset($excelFunctionCollection['ABS']);
    }

    public function testFunctionDefinitionLoadsAsObjectProperty(): void
    {
        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionDefinition = $excelFunctionCollection->ABS; // @phpstan-ignore-line
        self::assertInstanceOf(XlFunctionAbstract::class, $excelFunctionDefinition);
        self::assertSame('ABS', $excelFunctionDefinition->name);
    }

    public function testReadOnlyObjectAccess(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionDefinition = $excelFunctionCollection->ABS; // @phpstan-ignore-line
        self::assertInstanceOf(XlFunctionAbstract::class, $excelFunctionDefinition);
        self::assertSame('ABS', $excelFunctionDefinition->name);

        $excelFunctionCollection->ABS = 'NO LONGER ABS'; // @phpstan-ignore-line
    }

    public function testUnsetBlockedAsObjectProperty(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $excelFunctionCollection = new ExcelFunctions();
        $excelFunctionDefinition = $excelFunctionCollection->ABS; // @phpstan-ignore-line
        self::assertInstanceOf(XlFunctionAbstract::class, $excelFunctionDefinition);
        self::assertSame('ABS', $excelFunctionDefinition->name);

        unset($excelFunctionCollection->ABS);
    }
}
