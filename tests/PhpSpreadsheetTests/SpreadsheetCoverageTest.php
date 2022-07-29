<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Exception as SSException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class SpreadsheetCoverageTest extends TestCase
{
    public function testDocumentProperties(): void
    {
        $spreadsheet = new Spreadsheet();
        $properties = $spreadsheet->getProperties();
        $properties->setCreator('Anyone');
        $properties->setTitle('Description');
        $spreadsheet2 = new Spreadsheet();
        self::assertNotEquals($properties, $spreadsheet2->getProperties());
        $properties2 = clone $properties;
        $spreadsheet2->setProperties($properties2);
        self::assertEquals($properties, $spreadsheet2->getProperties());
        $spreadsheet->disconnectWorksheets();
        $spreadsheet2->disconnectWorksheets();
    }

    public function testDocumentSecurity(): void
    {
        $spreadsheet = new Spreadsheet();
        $security = $spreadsheet->getSecurity();
        $security->setLockRevision(true);
        $revisionsPassword = 'revpasswd';
        $security->setRevisionsPassword($revisionsPassword);
        $spreadsheet2 = new Spreadsheet();
        self::assertNotEquals($security, $spreadsheet2->getSecurity());
        $security2 = clone $security;
        $spreadsheet2->setSecurity($security2);
        self::assertEquals($security, $spreadsheet2->getSecurity());
        $spreadsheet->disconnectWorksheets();
        $spreadsheet2->disconnectWorksheets();
    }

    public function testCellXfCollection(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getFont()->setName('font1');
        $sheet->getStyle('A2')->getFont()->setName('font2');
        $sheet->getStyle('A3')->getFont()->setName('font3');
        $sheet->getStyle('B1')->getFont()->setName('font1');
        $sheet->getStyle('B2')->getFont()->setName('font2');
        $collection = $spreadsheet->getCellXfCollection();
        self::assertCount(4, $collection);
        $font1Style = $collection[1];
        self::assertTrue($spreadsheet->cellXfExists($font1Style));
        self::assertSame('font1', $spreadsheet->getCellXfCollection()[1]->getFont()->getName());
        self::assertSame('font1', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $sheet->getStyle('A3')->getFont()->getName());
        self::assertSame('font1', $sheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('B2')->getFont()->getName());

        $spreadsheet->removeCellXfByIndex(1);
        self::assertFalse($spreadsheet->cellXfExists($font1Style));
        self::assertSame('font2', $spreadsheet->getCellXfCollection()[1]->getFont()->getName());
        self::assertSame('Calibri', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $sheet->getStyle('A3')->getFont()->getName());
        self::assertSame('Calibri', $sheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('B2')->getFont()->getName());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidRemoveCellXfByIndex(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('CellXf index is out of bounds.');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getFont()->setName('font1');
        $sheet->getStyle('A2')->getFont()->setName('font2');
        $sheet->getStyle('A3')->getFont()->setName('font3');
        $sheet->getStyle('B1')->getFont()->setName('font1');
        $sheet->getStyle('B2')->getFont()->setName('font2');
        $spreadsheet->removeCellXfByIndex(5);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidRemoveDefaultStyle(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('No default style found for this workbook');
        // Removing default style probably should be disallowed.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->removeCellXfByIndex(0);
        $style = $spreadsheet->getDefaultStyle();
        $spreadsheet->disconnectWorksheets();
    }

    public function testCellStyleXF(): void
    {
        $spreadsheet = new Spreadsheet();
        $collection = $spreadsheet->getCellStyleXfCollection();
        self::assertCount(1, $collection);
        $styleXf = $collection[0];
        self::assertSame($styleXf, $spreadsheet->getCellStyleXfByIndex(0));
        $hash = $styleXf->getHashCode();
        self::assertSame($styleXf, $spreadsheet->getCellStyleXfByHashCode($hash));
        self::assertFalse($spreadsheet->getCellStyleXfByHashCode($hash . 'x'));
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidRemoveCellStyleXfByIndex(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('CellStyleXf index is out of bounds.');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->removeCellStyleXfByIndex(5);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidFirstSheetIndex(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('First sheet index must be a positive integer.');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setFirstSheetIndex(-1);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidVisibility(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('Invalid visibility value.');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setVisibility(Spreadsheet::VISIBILITY_HIDDEN);
        self::assertSame(Spreadsheet::VISIBILITY_HIDDEN, $spreadsheet->getVisibility());
        $spreadsheet->setVisibility(null);
        self::assertSame(Spreadsheet::VISIBILITY_VISIBLE, $spreadsheet->getVisibility());
        $spreadsheet->setVisibility('badvalue');
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidTabRatio(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('Tab ratio must be between 0 and 1000.');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setTabRatio(2000);
        $spreadsheet->disconnectWorksheets();
    }

    public function testCopy(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getFont()->setName('font1');
        $sheet->getStyle('A2')->getFont()->setName('font2');
        $sheet->getStyle('A3')->getFont()->setName('font3');
        $sheet->getStyle('B1')->getFont()->setName('font1');
        $sheet->getStyle('B2')->getFont()->setName('font2');
        $sheet->getCell('A1')->setValue('this is a1');
        $sheet->getCell('A2')->setValue('this is a2');
        $sheet->getCell('A3')->setValue('this is a3');
        $sheet->getCell('B1')->setValue('this is b1');
        $sheet->getCell('B2')->setValue('this is b2');
        $copied = $spreadsheet->copy();
        $copysheet = $copied->getActiveSheet();
        $copysheet->getStyle('A2')->getFont()->setName('font12');
        $copysheet->getCell('A2')->setValue('this was a2');

        self::assertSame('font1', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $sheet->getStyle('A3')->getFont()->getName());
        self::assertSame('font1', $sheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('B2')->getFont()->getName());
        self::assertSame('this is a1', $sheet->getCell('A1')->getValue());
        self::assertSame('this is a2', $sheet->getCell('A2')->getValue());
        self::assertSame('this is a3', $sheet->getCell('A3')->getValue());
        self::assertSame('this is b1', $sheet->getCell('B1')->getValue());
        self::assertSame('this is b2', $sheet->getCell('B2')->getValue());

        self::assertSame('font1', $copysheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font12', $copysheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $copysheet->getStyle('A3')->getFont()->getName());
        self::assertSame('font1', $copysheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $copysheet->getStyle('B2')->getFont()->getName());
        self::assertSame('this is a1', $copysheet->getCell('A1')->getValue());
        self::assertSame('this was a2', $copysheet->getCell('A2')->getValue());
        self::assertSame('this is a3', $copysheet->getCell('A3')->getValue());
        self::assertSame('this is b1', $copysheet->getCell('B1')->getValue());
        self::assertSame('this is b2', $copysheet->getCell('B2')->getValue());

        $spreadsheet->disconnectWorksheets();
        $copied->disconnectWorksheets();
    }

    public function testClone(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('Do not use clone on spreadsheet. Use spreadsheet->copy() instead.');
        $spreadsheet = new Spreadsheet();
        $clone = clone $spreadsheet;
        $spreadsheet->disconnectWorksheets();
        $clone->disconnectWorksheets();
    }
}
