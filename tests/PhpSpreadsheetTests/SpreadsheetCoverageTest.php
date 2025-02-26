<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Exception as SSException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetCoverageTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $spreadsheet2 = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->spreadsheet2 !== null) {
            $this->spreadsheet2->disconnectWorksheets();
            $this->spreadsheet2 = null;
        }
    }

    public function testDocumentProperties(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $properties = $this->spreadsheet->getProperties();
        $properties->setCreator('Anyone');
        $properties->setTitle('Description');
        $this->spreadsheet2 = new Spreadsheet();
        self::assertNotEquals($properties, $this->spreadsheet2->getProperties());
        $properties2 = clone $properties;
        $this->spreadsheet2->setProperties($properties2);
        self::assertEquals($properties, $this->spreadsheet2->getProperties());
    }

    public function testDocumentSecurity(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $security = $this->spreadsheet->getSecurity();
        $security->setLockRevision(true);
        $revisionsPassword = 'revpasswd';
        $security->setRevisionsPassword($revisionsPassword);
        $this->spreadsheet2 = new Spreadsheet();
        self::assertNotEquals($security, $this->spreadsheet2->getSecurity());
        $security2 = clone $security;
        $this->spreadsheet2->setSecurity($security2);
        self::assertEquals($security, $this->spreadsheet2->getSecurity());
    }

    public function testCellXfCollection(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getFont()->setName('font1');
        $sheet->getStyle('A2')->getFont()->setName('font2');
        $sheet->getStyle('A3')->getFont()->setName('font3');
        $sheet->getStyle('B1')->getFont()->setName('font1');
        $sheet->getStyle('B2')->getFont()->setName('font2');
        $collection = $this->spreadsheet->getCellXfCollection();
        self::assertCount(4, $collection);
        $font1Style = $collection[1];
        self::assertTrue($this->spreadsheet->cellXfExists($font1Style));
        self::assertSame('font1', $this->spreadsheet->getCellXfCollection()[1]->getFont()->getName());
        self::assertSame('font1', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $sheet->getStyle('A3')->getFont()->getName());
        self::assertSame('font1', $sheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('B2')->getFont()->getName());

        $this->spreadsheet->removeCellXfByIndex(1);
        self::assertFalse($this->spreadsheet->cellXfExists($font1Style));
        self::assertSame('font2', $this->spreadsheet->getCellXfCollection()[1]->getFont()->getName());
        self::assertSame('Calibri', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $sheet->getStyle('A3')->getFont()->getName());
        self::assertSame('Calibri', $sheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('B2')->getFont()->getName());
    }

    public function testInvalidRemoveCellXfByIndex(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('CellXf index is out of bounds.');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getFont()->setName('font1');
        $sheet->getStyle('A2')->getFont()->setName('font2');
        $sheet->getStyle('A3')->getFont()->setName('font3');
        $sheet->getStyle('B1')->getFont()->setName('font1');
        $sheet->getStyle('B2')->getFont()->setName('font2');
        $this->spreadsheet->removeCellXfByIndex(5);
    }

    public function testInvalidRemoveDefaultStyle(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('No default style found for this workbook');
        // Removing default style probably should be disallowed.
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->removeCellXfByIndex(0);
        $this->spreadsheet->getDefaultStyle();
    }

    public function testCellStyleXF(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $collection = $this->spreadsheet->getCellStyleXfCollection();
        self::assertCount(1, $collection);
        $styleXf = $collection[0];
        self::assertSame($styleXf, $this->spreadsheet->getCellStyleXfByIndex(0));
        $hash = $styleXf->getHashCode();
        self::assertSame($styleXf, $this->spreadsheet->getCellStyleXfByHashCode($hash));
        self::assertFalse($this->spreadsheet->getCellStyleXfByHashCode($hash . 'x'));
    }

    public function testInvalidRemoveCellStyleXfByIndex(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('CellStyleXf index is out of bounds.');
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->removeCellStyleXfByIndex(5);
    }

    public function testInvalidFirstSheetIndex(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('First sheet index must be a positive integer.');
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->setFirstSheetIndex(-1);
    }

    public function testInvalidVisibility(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('Invalid visibility value.');
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->setVisibility(Spreadsheet::VISIBILITY_HIDDEN);
        self::assertSame(Spreadsheet::VISIBILITY_HIDDEN, $this->spreadsheet->getVisibility());
        $this->spreadsheet->setVisibility(null);
        self::assertSame(Spreadsheet::VISIBILITY_VISIBLE, $this->spreadsheet->getVisibility());
        $this->spreadsheet->setVisibility('badvalue');
    }

    public function testInvalidTabRatio(): void
    {
        $this->expectException(SSException::class);
        $this->expectExceptionMessage('Tab ratio must be between 0 and 1000.');
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->setTabRatio(2000);
    }
}
