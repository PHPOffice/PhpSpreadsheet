<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class NamedRangeTest extends TestCase
{
    /** @var Spreadsheet */
    private $spreadsheet;

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
        self::assertSame(
            '=B1',
            $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getActiveSheet())->getValue()
        );
    }

    public function testAddScopedNamedRangeWithSameName(): void
    {
        $this->spreadsheet->addNamedRange(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addNamedRange(
            new NamedRange('FOO', $this->spreadsheet->getSheetByName('Sheet #2'), '=B1', true)
        );

        self::assertCount(2, $this->spreadsheet->getNamedRanges());
        self::assertSame(
            '=A1',
            $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getActiveSheet())->getValue()
        );
        self::assertSame(
            '=B1',
            $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getSheetByName('Sheet #2'))->getValue()
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
            new NamedRange('FOO', $this->spreadsheet->getSheetByName('Sheet #2'), '=B1', true)
        );

        $this->spreadsheet->removeNamedRange('Foo', $this->spreadsheet->getActiveSheet());

        self::assertCount(1, $this->spreadsheet->getNamedRanges());
        self::assertSame(
            '=B1',
            $this->spreadsheet->getNamedRange('foo', $this->spreadsheet->getSheetByName('Sheet #2'))->getValue()
        );
    }

    public function testRemoveScopedNamedRangeWhenDuplicateNames(): void
    {
        $this->spreadsheet->addNamedRange(
            new NamedRange('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addNamedRange(
            new NamedRange('FOO', $this->spreadsheet->getSheetByName('Sheet #2'), '=B1', true)
        );

        $this->spreadsheet->removeNamedRange('Foo', $this->spreadsheet->getSheetByName('Sheet #2'));

        self::assertCount(1, $this->spreadsheet->getNamedRanges());
        self::assertSame(
            '=A1',
            $this->spreadsheet->getNamedRange('foo')->getValue()
        );
    }
}
