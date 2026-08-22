<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PHPUnit\Framework\TestCase;

class HyperlinkTest extends TestCase
{
    public function testVariableColumns(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 3);
        $sheet->setCellValue('B1', 4);
        $sheet->setCellValue('C1', 5);
        $sheet->setCellValue('A2', 6);
        $sheet->setCellValue('B2', 'hyperlink');
        $sheet->getCell('B2')->getHyperlink()
            ->setUrl('http://www.example.com');
        $sheet->setCellValue('C2', 8);

        $fh = fopen('php://memory', 'r+b');
        self::assertNotFalse($fh);
        $writer = new Csv($spreadsheet);
        self::assertFalse($writer->getPreferHyperlinkToLabel());
        $writer->setEnclosureRequired(false)->setLineEnding("\n");
        $writer->save($fh);
        rewind($fh);
        self::assertSame(
            "3,4,5\n6,hyperlink,8\n",
            stream_get_contents($fh)
        );

        rewind($fh);
        $writer->setPreferHyperlinkToLabel(true);
        $writer->save($fh);
        rewind($fh);
        self::assertSame(
            "3,4,5\n6,http://www.example.com,8\n",
            stream_get_contents($fh)
        );
        fclose($fh);
        $spreadsheet->disconnectWorksheets();
    }
}
