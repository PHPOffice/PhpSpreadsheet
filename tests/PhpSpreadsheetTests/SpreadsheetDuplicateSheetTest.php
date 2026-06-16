<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetDuplicateSheetTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testDuplicate(): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle('original');
        $sheet->getCell('A1')->setValue('text1');
        $sheet->getCell('A2')->setValue('text2');
        $sheet->getStyle('A1')
            ->getFont()
            ->setBold(true);
        $sheet3 = $this->spreadsheet->createSheet();
        $sheet3->setTitle('added');
        $newSheet = $this->spreadsheet
            ->duplicateWorksheetByTitle('original');
        $this->spreadsheet->duplicateWorksheetByTitle('added');
        self::assertSame('original 1', $newSheet->getTitle());
        self::assertSame(
            'text1',
            $newSheet->getCell('A1')->getValue()
        );
        self::assertSame(
            'text2',
            $newSheet->getCell('A2')->getValue()
        );
        self::assertTrue(
            $newSheet->getStyle('A1')->getFont()->getBold()
        );
        self::assertFalse(
            $newSheet->getStyle('A2')->getFont()->getBold()
        );
        $sheetNames = [];
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $sheetNames[] = $worksheet->getTitle();
        }
        $expected = ['original', 'original 1', 'added', 'added 1'];
        self::assertSame($expected, $sheetNames);
    }
}
