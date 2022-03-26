<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions\XlAbs;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PHPUnit\Framework\TestCase;

class XlFunctionSignatureTest extends TestCase
{
    public function testArrayAccess(): void
    {
        $functionDefinition = new XlAbs();

        self::assertTrue(isset($functionDefinition['name']));
        self::assertSame('ABS', $functionDefinition['name']);
        self::assertSame(Category::CATEGORY_MATH_AND_TRIG, $functionDefinition['category']);
        self::assertIsCallable($functionDefinition['functionCall']);
        self::assertSame('1', $functionDefinition['argumentCount']);
    }

    public function testReadOnlyArrayAccess(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $functionDefinition = new XlAbs();
        $functionDefinition['name'] = 'NO LONGER ABS';
    }

    public function testUnsetBlockedAsArray(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $functionDefinition = new XlAbs();
        unset($functionDefinition['name']);
    }

    public function testObjectAccess(): void
    {
        $functionDefinition = new XlAbs();

        self::assertTrue(isset($functionDefinition->name));
        self::assertSame('ABS', $functionDefinition->name);
        self::assertSame(Category::CATEGORY_MATH_AND_TRIG, $functionDefinition->category);
        self::assertIsCallable($functionDefinition->functionCall);
        self::assertSame('1', $functionDefinition->argumentCount);
    }

    public function testReadOnlyObjectAccess(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $functionDefinition = new XlAbs();
        $functionDefinition->name = 'NO LONGER ABS';
    }

    public function testUnsetBlockedAsObject(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Action not permitted');

        $functionDefinition = new XlAbs();
        unset($functionDefinition->name);
    }
}
