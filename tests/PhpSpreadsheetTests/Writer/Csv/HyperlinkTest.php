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

    public function testPreferHyperlinkLeavesTheSheetAlone(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'link');
        $sheet->getCell('A1')->getHyperlink()->setUrl('http://www.example.com');
        $sheet->setCellValue('C1', 'plain');

        $fh = fopen('php://memory', 'r+b');
        self::assertNotFalse($fh);
        $writer = new Csv($spreadsheet);
        $writer->setEnclosureRequired(false)->setLineEnding("\n")->setPreferHyperlinkToLabel(true);
        $writer->save($fh);
        rewind($fh);
        self::assertSame("http://www.example.com,,plain\n", stream_get_contents($fh));
        fclose($fh);

        self::assertSame(['A1'], array_keys($sheet->getHyperlinkCollection()));
        self::assertFalse($sheet->cellExists('B1'));
        $spreadsheet->disconnectWorksheets();
    }
}
