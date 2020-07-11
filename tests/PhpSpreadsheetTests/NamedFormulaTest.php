<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class NamedFormulaTest extends TestCase
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
        $this->spreadsheet->addNamedFormula(
            new NamedFormula('Foo', $this->spreadsheet->getActiveSheet(), '=19%')
        );

        self::assertCount(1, count($this->spreadsheet->getDefinedNames()));
        self::assertCount(1, count($this->spreadsheet->getNamedFormulae()));
        self::assertCount(0, count($this->spreadsheet->getNamedRanges()));
    }

    public function testAddDuplicateNamedRange(): void
    {
        $this->spreadsheet->addNamedFormula(
            new NamedFormula('Foo', $this->spreadsheet->getActiveSheet(), '=19%')
        );
        $this->spreadsheet->addNamedFormula(
            new NamedFormula('FOO', $this->spreadsheet->getActiveSheet(), '=16%')
        );

        self::assertCount(1, count($this->spreadsheet->getNamedFormulae()));
        self::assertSame(
            '=16%',
            $this->spreadsheet->getNamedFormula('foo', $this->spreadsheet->getActiveSheet())->getValue()
        );
    }

    public function testAddScopedNamedFormulaWithSameName(): void
    {
        $this->spreadsheet->addNamedFormula(
            new NamedFormula('Foo', $this->spreadsheet->getActiveSheet(), '=19%')
        );
        $this->spreadsheet->addNamedFormula(
            new NamedFormula('FOO', $this->spreadsheet->getSheetByName('Sheet #2'), '=16%', true)
        );

        self::assertCount(2, count($this->spreadsheet->getNamedFormulae()));
        self::assertSame(
            '=19%',
            $this->spreadsheet->getNamedFormula('foo', $this->spreadsheet->getActiveSheet())->getValue()
        );
        self::assertSame(
            '=16%',
            $this->spreadsheet->getNamedFormula('foo', $this->spreadsheet->getSheetByName('Sheet #2'))->getValue()
        );
    }

    public function testRemoveNamedFormula()
    {
        $this->spreadsheet->addDefinedName(
            new NamedFormula('Foo', $this->spreadsheet->getActiveSheet(), '=16%')
        );
        $this->spreadsheet->addDefinedName(
            new NamedFormula('Bar', $this->spreadsheet->getActiveSheet(), '=19%')
        );

        $this->spreadsheet->removeNamedFormula('Foo', $this->spreadsheet->getActiveSheet());

        self::assertCount(1, count($this->spreadsheet->getNamedFormulae()));
    }
}
