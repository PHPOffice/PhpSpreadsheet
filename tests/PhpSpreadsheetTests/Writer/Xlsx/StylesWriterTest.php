<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class StylesWriterTest extends TestCase
{
    public function testStylesWriter(): void
    {
        $spreadsheet = new Spreadsheet();

        $writer = new XlsxWriter($spreadsheet);
        $writer->createStyleDictionaries();
        $writerStyle = new XlsxWriter\Style($writer);
        $data = $writerStyle->writeStyles($spreadsheet);
        self::assertStringContainsString(
            '<fonts count="1"><font><b val="0"/><i val="0"/><strike val="0"/><u val="none"/><sz val="11"/><color rgb="FF000000"/><name val="Calibri"/></font></fonts>',
            $data
        );
        $spreadsheet->disconnectWorksheets();
    }
}
