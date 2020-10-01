<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class DefinedNameTest extends TestCase
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

    public function testAddDefinedName(): void
    {
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );

        self::assertCount(1, $this->spreadsheet->getDefinedNames());
    }

    public function testAddDuplicateDefinedName(): void
    {
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('FOO', $this->spreadsheet->getActiveSheet(), '=B1')
        );

        self::assertCount(1, $this->spreadsheet->getDefinedNames());
        self::assertSame(
            '=B1',
            $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getActiveSheet())->getValue()
        );
    }

    public function testAddScopedDefinedNameWithSameName(): void
    {
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('FOO', $this->spreadsheet->getSheetByName('Sheet #2'), '=B1', true)
        );

        self::assertCount(2, $this->spreadsheet->getDefinedNames());
        self::assertSame(
            '=A1',
            $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getActiveSheet())->getValue()
        );
        self::assertSame(
            '=B1',
            $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getSheetByName('Sheet #2'))->getValue()
        );
    }

    public function testRemoveDefinedName(): void
    {
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('Bar', $this->spreadsheet->getActiveSheet(), '=B1')
        );

        $this->spreadsheet->removeDefinedName('Foo', $this->spreadsheet->getActiveSheet());

        self::assertCount(1, $this->spreadsheet->getDefinedNames());
    }

    public function testRemoveGlobalDefinedNameWhenDuplicateNames(): void
    {
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('FOO', $this->spreadsheet->getSheetByName('Sheet #2'), '=B1', true)
        );

        $this->spreadsheet->removeDefinedName('Foo', $this->spreadsheet->getActiveSheet());

        self::assertCount(1, $this->spreadsheet->getDefinedNames());
        self::assertSame(
            '=B1',
            $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getSheetByName('Sheet #2'))->getValue()
        );
    }

    public function testRemoveScopedDefinedNameWhenDuplicateNames(): void
    {
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('Foo', $this->spreadsheet->getActiveSheet(), '=A1')
        );
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('FOO', $this->spreadsheet->getSheetByName('Sheet #2'), '=B1', true)
        );

        $this->spreadsheet->removeDefinedName('Foo', $this->spreadsheet->getSheetByName('Sheet #2'));

        self::assertCount(1, $this->spreadsheet->getDefinedNames());
        self::assertSame(
            '=A1',
            $this->spreadsheet->getDefinedName('foo')->getValue()
        );
    }
}
