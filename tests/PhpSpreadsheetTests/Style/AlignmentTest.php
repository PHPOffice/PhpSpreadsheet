<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PHPUnit\Framework\TestCase;

class AlignmentTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testAlignment(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('Cell1');
        $cell1->getStyle()->getAlignment()->setTextRotation(45);
        self::assertEquals(45, $cell1->getStyle()->getAlignment()->getTextRotation());
        $cell2 = $sheet->getCell('A2');
        $cell2->setValue('Cell2');
        $cell2->getStyle()->getAlignment()->setTextRotation(-45);
        self::assertEquals(-45, $cell2->getStyle()->getAlignment()->getTextRotation());
        // special value for stacked
        $cell3 = $sheet->getCell('A3');
        $cell3->setValue('Cell3');
        $cell3->getStyle()->getAlignment()->setTextRotation(255);
        self::assertEquals(-165, $cell3->getStyle()->getAlignment()->getTextRotation());
    }

    public function testRotationTooHigh(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('Cell1');
        $cell1->getStyle()->getAlignment()->setTextRotation(91);
        self::assertEquals(0, $cell1->getStyle()->getAlignment()->getTextRotation());
    }

    public function testRotationTooLow(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('Cell1');
        $cell1->getStyle()->getAlignment()->setTextRotation(-91);
        self::assertEquals(0, $cell1->getStyle()->getAlignment()->getTextRotation());
    }

    public function testHorizontal(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('X');
        $cell1->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
        self::assertEquals(Alignment::HORIZONTAL_LEFT, $cell1->getStyle()->getAlignment()->getHorizontal());
        self::assertEquals(1, $cell1->getStyle()->getAlignment()->getIndent());
        $cell2 = $sheet->getCell('A2');
        $cell2->setValue('Y');
        $cell2->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setIndent(2);
        self::assertEquals(Alignment::HORIZONTAL_RIGHT, $cell2->getStyle()->getAlignment()->getHorizontal());
        self::assertEquals(2, $cell2->getStyle()->getAlignment()->getIndent());
        $cell3 = $sheet->getCell('A3');
        $cell3->setValue('Z');
        // indent not supported for next style - changed to 0
        $cell3->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER_CONTINUOUS)->setIndent(3);
        self::assertEquals(Alignment::HORIZONTAL_CENTER_CONTINUOUS, $cell3->getStyle()->getAlignment()->getHorizontal());
        self::assertEquals(0, $cell3->getStyle()->getAlignment()->getIndent());
    }

    public function testReadOrder(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('ABC');
        $cell1->getStyle()->getAlignment()->setReadOrder(0);
        self::assertEquals(0, $cell1->getStyle()->getAlignment()->getReadOrder());
        $cell1->getStyle()->getAlignment()->setReadOrder(1);
        self::assertEquals(1, $cell1->getStyle()->getAlignment()->getReadOrder());
        // following not supported - zero is used instead
        $cell1->getStyle()->getAlignment()->setReadOrder(-1);
        self::assertEquals(0, $cell1->getStyle()->getAlignment()->getReadOrder());
        $cell1->getStyle()->getAlignment()->setReadOrder(2);
        self::assertEquals(2, $cell1->getStyle()->getAlignment()->getReadOrder());
        // following not supported - zero is used instead
        $cell1->getStyle()->getAlignment()->setReadOrder(3);
        self::assertEquals(0, $cell1->getStyle()->getAlignment()->getReadOrder());
    }
}
