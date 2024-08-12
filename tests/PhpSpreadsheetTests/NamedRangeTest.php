<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class NamedRangeTest extends TestCase
{
    private Spreadsheet $spreadsheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->spreadsheet = new Spreadsheet();

        $this->spreadsheet->getActiveSheet()
            ->setTitle('Sheet #1');

        $worksheet2 = new Worksheet();
        $worksheet2->setTitle('Sheet #2');
        $this->spreadsheet->addSheet($worksheet2);

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    public function testAddNamedRange(): void
    {
        $this->spreadsheet->addNamedRange(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );

        self::assertCount(1, $this->spreadsheet->getDefinedNames());
        self::assertCount(1, $this->spreadsheet->getNamedRanges());
        self::assertCount(0, $this->spreadsheet->getNamedFormulae());
    }

    public function testAddDuplicateNamedRange(): void
    {
        $this->spreadsheet->addNamedRange(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addNamedRange(
            new NamedRange('FOO', $this->spreadsheet->getActiveSheet(), '=B1')
        );

        self::assertCount(1, $this->spreadsheet->getNamedRanges());
        $range = $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getActiveSheet());
        self::assertNotNull($range);
        self::assertSame(
            '=B1',
            $range->getValue()
        );
    }

    public function testAddScopedNamedRangeWithSameName(): void
    {
        $this->spreadsheet->addNamedRange(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addNamedRange(
            new NamedRange('FOO', $this->spreadsheet->getSheetByNameOrThrow('Sheet #2'), '=B1', true)
        );

        self::assertCount(2, $this->spreadsheet->getNamedRanges());
        $range = $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getActiveSheet());
        self::assertNotNull($range);
        self::assertSame(
            '=A1',
            $range->getValue()
        );
        $range = $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getSheetByNameOrThrow('Sheet #2'));
        self::assertNotNull($range);
        self::assertSame(
            '=B1',
            $range->getValue()
        );
    }

    public function testRemoveNamedRange(): void
    {
        $this->spreadsheet->addDefinedName(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addDefinedName(
            new NamedRange('Bar', $this->spreadsheet->getActiveSheet(), '=B1')
        );

        $this->spreadsheet->removeNamedRange('Foo', $this->spreadsheet->getActiveSheet());

        self::assertCount(1, $this->spreadsheet->getNamedRanges());
    }

    public function testRemoveGlobalNamedRangeWhenDuplicateNames(): void
    {
        $this->spreadsheet->addNamedRange(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addNamedRange(
            new NamedRange('FOO', $this->spreadsheet->getSheetByNameOrThrow('Sheet #2'), '=B1', true)
        );

        $this->spreadsheet->removeNamedRange('Foo', $this->spreadsheet->getActiveSheet());

        self::assertCount(1, $this->spreadsheet->getNamedRanges());
        $sheet = $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getSheetByNameOrThrow('Sheet #2'));
        self::assertNotNull($sheet);
        self::assertSame(
            '=B1',
            $sheet->getValue()
        );
    }

    public function testRemoveScopedNamedRangeWhenDuplicateNames(): void
    {
        $this->spreadsheet->addNamedRange(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addNamedRange(
            new NamedRange('FOO', $this->spreadsheet->getSheetByNameOrThrow('Sheet #2'), '=B1', true)
        );

        $this->spreadsheet->removeNamedRange('Foo', $this->spreadsheet->getSheetByNameOrThrow('Sheet #2'));

        self::assertCount(1, $this->spreadsheet->getNamedRanges());
        $range = $this->spreadsheet->getNamedRange('foo');
        self::AssertNotNull($range);
        self::assertSame(
            '=A1',
            $range->getValue()
        );
    }

    public function testRemoveNonExistentNamedRange(): void
    {
        self::assertCount(0, $this->spreadsheet->getNamedRanges());
        $this->spreadsheet->removeNamedRange('Any');
    }
}
