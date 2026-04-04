<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PHPUnit\Framework\TestCase;

class PrintArea2Test extends TestCase
{
    private Spreadsheet $spreadsheet;

    protected function tearDown(): void
    {
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function testNegativeScale(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Scale must not be negative');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setScale(-1);
    }

    public function testColumnRepeat(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setColumnsToRepeatAtLeftByStartAndEnd('A', 'B');
        self::assertSame(['A', 'B'], $pageSetup->getColumnsToRepeatAtLeft());
    }

    public function testPrintAreaIndex(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('A1:B2,C1:D3');
        self::assertTrue($pageSetup->isPrintAreaSet());
        self::assertTrue($pageSetup->isPrintAreaSet(1));
        self::assertTrue($pageSetup->isPrintAreaSet(2));
        self::assertFalse($pageSetup->isPrintAreaSet(3));
        self::assertSame('A1:B2,C1:D3', $pageSetup->getPrintArea());
        self::assertSame('A1:B2', $pageSetup->getPrintArea(1));
        self::assertSame('C1:D3', $pageSetup->getPrintArea(2));

        try {
            $pageSetup->getPrintArea(3);
            self::fail('Should have thrown Exception');
        } catch (PhpSpreadsheetException $e) {
            self::assertSame('Requested Print Area does not exist', $e->getMessage());
        }
    }

    public function testClearPrintArea(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('A1:B2,D3:F5,G6:I8');
        self::assertSame($pageSetup->getPrintArea(), 'A1:B2,D3:F5,G6:I8');
        $pageSetup->clearPrintArea(9);
        self::assertSame($pageSetup->getPrintArea(), 'A1:B2,D3:F5,G6:I8');
        $pageSetup->clearPrintArea(2);
        self::assertSame($pageSetup->getPrintArea(), 'A1:B2,G6:I8');
        $pageSetup->clearPrintArea();
        self::assertSame('', $pageSetup->getPrintArea());
    }

    public function testBadSetPrintArea1(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate must not specify a worksheet.');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('Sheet1!A1:B1');
    }

    public function testBadSetPrintArea2(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate must be a range of cells.');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('A1');
    }

    public function testBadSetPrintArea3(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Cell coordinate must not be absolute.');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('$A$1:$B$1');
    }

    public function testBadSetPrintArea4(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Invalid method for setting print range.');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('A1:B1', 0, 'unknownmethod');
    }

    public function testBadSetPrintArea5(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Invalid index for setting print range.');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('A1:B2,C3:D5,E6:G8,I12:J13');
        $pageSetup->setPrintArea('X98:X99', 7);
    }

    public function testBadSetPrintArea6(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Invalid index for setting print range.');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('A1:B2,C3:D5,E6:G8,I12:J13');
        $pageSetup->setPrintArea('X98:X99', 7, PageSetup::SETPRINTRANGE_INSERT);
    }

    public function testGoodSetPrintArea1(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setPrintArea('A1:B2,C3:D5,E6:G8,I12:J13');
        $pageSetup->setPrintArea('X98:X99', 2);
        self::assertSame('A1:B2,X98:X99,E6:G8,I12:J13', $pageSetup->getPrintArea());
        $pageSetup->setPrintArea('Y88:Y89', -2);
        self::assertSame('A1:B2,X98:X99,Y88:Y89,I12:J13', $pageSetup->getPrintArea());
        $pageSetup->setPrintArea('Z78:Z79', 0, PageSetup::SETPRINTRANGE_INSERT);
        self::assertSame('A1:B2,X98:X99,Y88:Y89,I12:J13,Z78:Z79', $pageSetup->getPrintArea());
        $pageSetup->setPrintArea('W68:W69', -1, PageSetup::SETPRINTRANGE_INSERT);
        self::assertSame('W68:W69,A1:B2,X98:X99,Y88:Y89,I12:J13,Z78:Z79', $pageSetup->getPrintArea());
        $pageSetup->setPrintArea('V58:V59', 1, PageSetup::SETPRINTRANGE_INSERT);
        self::assertSame('W68:W69,V58:V59,A1:B2,X98:X99,Y88:Y89,I12:J13,Z78:Z79', $pageSetup->getPrintArea());
        $pageSetup->addPrintArea('U48:U49');
        self::assertSame('U48:U49,W68:W69,V58:V59,A1:B2,X98:X99,Y88:Y89,I12:J13,Z78:Z79', $pageSetup->getPrintArea());
        $pageSetup->setPrintAreaByColumnAndRow(1,2,3,4);
        self::assertSame('A2:C4', $pageSetup->getPrintArea());
    }

    public function testFirstPageNumber(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $pageSetup = $sheet->getPageSetup();
        $pageSetup->setFirstPageNumber(6);
        self::assertSame(6, $pageSetup->getFirstPageNumber());
        $pageSetup->resetFirstPageNumber();
        self::assertNull($pageSetup->getFirstPageNumber());
    }
}
