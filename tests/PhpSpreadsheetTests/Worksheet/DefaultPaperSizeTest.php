<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PHPUnit\Framework\TestCase;

class DefaultPaperSizeTest extends TestCase
{
    /** @var int */
    private $paperSize;

    /** @var string */
    private $orientation;

    protected function setUp(): void
    {
        $this->paperSize = PageSetup::getPaperSizeDefault();
        $this->orientation = PageSetup::getOrientationDefault();
    }

    protected function tearDown(): void
    {
        PageSetup::setPaperSizeDefault($this->paperSize);
        PageSetup::setOrientationDefault($this->orientation);
    }

    public function testChangeDefault(): void
    {
        PageSetup::setPaperSizeDefault(PageSetup::PAPERSIZE_A4);
        PageSetup::setOrientationDefault(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        self::assertSame(PageSetup::PAPERSIZE_A4, $sheet1->getPageSetup()->getPaperSize());
        self::assertSame(PageSetup::PAPERSIZE_A4, $sheet2->getPageSetup()->getPaperSize());
        self::assertSame(PageSetup::PAPERSIZE_A4, $sheet3->getPageSetup()->getPaperSize());
        self::assertSame(PageSetup::ORIENTATION_LANDSCAPE, $sheet1->getPageSetup()->getOrientation());
        self::assertSame(PageSetup::ORIENTATION_LANDSCAPE, $sheet2->getPageSetup()->getOrientation());
        self::assertSame(PageSetup::ORIENTATION_LANDSCAPE, $sheet3->getPageSetup()->getOrientation());
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnchangedDefault(): void
    {
        //PageSetup::setPaperSizeDefault(PageSetup::PAPERSIZE_A4);
        //PageSetup::setOrientationDefault(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet3 = $spreadsheet->createSheet();
        self::assertSame(PageSetup::PAPERSIZE_LETTER, $sheet1->getPageSetup()->getPaperSize());
        self::assertSame(PageSetup::PAPERSIZE_LETTER, $sheet2->getPageSetup()->getPaperSize());
        self::assertSame(PageSetup::PAPERSIZE_LETTER, $sheet3->getPageSetup()->getPaperSize());
        self::assertSame(PageSetup::ORIENTATION_DEFAULT, $sheet1->getPageSetup()->getOrientation());
        self::assertSame(PageSetup::ORIENTATION_DEFAULT, $sheet2->getPageSetup()->getOrientation());
        self::assertSame(PageSetup::ORIENTATION_DEFAULT, $sheet3->getPageSetup()->getOrientation());
        $spreadsheet->disconnectWorksheets();
    }
}
