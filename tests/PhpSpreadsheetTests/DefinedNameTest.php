<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\NamedRange;
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
        $definedName = $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getActiveSheet());
        self::assertNotNull($definedName);
        self::assertSame('=B1', $definedName->getValue());
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
        $definedName1 = $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getActiveSheet());
        self::assertNotNull($definedName1);
        self::assertSame('=A1', $definedName1->getValue());

        $definedName2 = $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getSheetByName('Sheet #2'));
        self::assertNotNull($definedName2);
        self::assertSame('=B1', $definedName2->getValue());
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
        $definedName = $this->spreadsheet->getDefinedName('foo', $this->spreadsheet->getSheetByName('Sheet #2'));
        self::assertNotNull($definedName);
        self::assertSame('=B1', $definedName->getValue());
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
        $definedName = $this->spreadsheet->getDefinedName('foo');
        self::assertNotNull($definedName);
        self::assertSame('=A1', $definedName->getValue());
    }

    public function testDefinedNameNoWorksheetNoScope(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        new NamedRange('xyz');
    }

    public function testSetAndGetRange(): void
    {
        $this->spreadsheet->addDefinedName(
            DefinedName::createInstance('xyz', $this->spreadsheet->getActiveSheet(), 'A1')
        );

        /** @var NamedRange $namedRange */
        $namedRange = $this->spreadsheet->getDefinedName('XYZ');
        self::assertInstanceOf(NamedRange::class, $namedRange);
        self::assertEquals('A1', $namedRange->getRange());
        self::assertEquals('A1', $namedRange->getValue());
        $namedRange->setRange('A2');
        self::assertEquals('A2', $namedRange->getValue());
    }

    public function testChangeWorksheet(): void
    {
        $sheet1 = $this->spreadsheet->getSheetByName('Sheet #1');
        $sheet2 = $this->spreadsheet->getSheetByName('Sheet #2');
        self::assertNotNull($sheet1);
        self::assertNotNull($sheet2);

        $sheet1->getCell('A1')->setValue(1);
        $sheet2->getCell('A1')->setValue(2);
        $namedRange = new NamedRange('xyz', $sheet2, '$A$1');
        $namedRange->setWorksheet($sheet1);
        $this->spreadsheet->addNamedRange($namedRange);
        $sheet1->getCell('B2')->setValue('=XYZ');
        self::assertEquals(1, $sheet1->getCell('B2')->getCalculatedValue());
        $sheet2->getCell('B2')->setValue('=XYZ');
        self::assertEquals(1, $sheet2->getCell('B2')->getCalculatedValue());
    }

    public function testLocalOnly(): void
    {
        $sheet1 = $this->spreadsheet->getSheetByName('Sheet #1');
        $sheet2 = $this->spreadsheet->getSheetByName('Sheet #2');
        self::assertNotNull($sheet1);
        self::assertNotNull($sheet2);

        $sheet1->getCell('A1')->setValue(1);
        $sheet2->getCell('A1')->setValue(2);
        $namedRange = new NamedRange('abc', $sheet2, '$A$1');
        $namedRange->setWorksheet($sheet1)->setLocalOnly(true);
        $this->spreadsheet->addNamedRange($namedRange);
        $sheet1->getCell('C2')->setValue('=ABC');
        self::assertEquals(1, $sheet1->getCell('C2')->getCalculatedValue());
        $sheet2->getCell('C2')->setValue('=ABC');
        self::assertEquals('#NAME?', $sheet2->getCell('C2')->getCalculatedValue());
    }

    public function testScope(): void
    {
        $sheet1 = $this->spreadsheet->getSheetByName('Sheet #1');
        $sheet2 = $this->spreadsheet->getSheetByName('Sheet #2');
        self::assertNotNull($sheet1);
        self::assertNotNull($sheet2);

        $sheet1->getCell('A1')->setValue(1);
        $sheet2->getCell('A1')->setValue(2);
        $namedRange = new NamedRange('abc', $sheet2, '$A$1');
        $namedRange->setScope($sheet1);
        $this->spreadsheet->addNamedRange($namedRange);
        $sheet1->getCell('C2')->setValue('=ABC');
        self::assertEquals(2, $sheet1->getCell('C2')->getCalculatedValue());
        $sheet2->getCell('C2')->setValue('=ABC');
        self::assertEquals('#NAME?', $sheet2->getCell('C2')->getCalculatedValue());
    }

    public function testClone(): void
    {
        $sheet1 = $this->spreadsheet->getSheetByName('Sheet #1');
        $sheet2 = $this->spreadsheet->getSheetByName('Sheet #2');
        self::assertNotNull($sheet1);
        self::assertNotNull($sheet2);

        $sheet1->getCell('A1')->setValue(1);
        $sheet2->getCell('A1')->setValue(2);
        $namedRange = new NamedRange('abc', $sheet2, '$A$1');
        $namedRangeClone = clone $namedRange;
        $ss1 = $namedRange->getWorksheet();
        $ss2 = $namedRangeClone->getWorksheet();
        self::assertNotNull($ss1);
        self::assertNotNull($ss2);
        self::assertNotSame($ss1, $ss2);
        self::assertEquals($ss1->getTitle(), $ss2->getTitle());
    }
}
