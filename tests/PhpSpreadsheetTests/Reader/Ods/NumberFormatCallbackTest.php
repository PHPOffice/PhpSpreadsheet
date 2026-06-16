<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Style;
use PHPUnit\Framework\TestCase;

class NumberFormatCallbackTest extends TestCase
{
    private string $tempfile = '';

    protected function tearDown(): void
    {
        if ($this->tempfile !== '') {
            unlink($this->tempfile);
            $this->tempfile = '';
        }
    }

    public function testCallbacks(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $sheet->getCell('A1')->setValue(1.23);
        $sheet->getCell('A2')->setValue(2.34);
        $sheet->getStyle('A2')->getNumberFormat()
            ->setFormatCode('¤#,##0.000');
        $sheet->getCell('A3')->setValue(3.45);
        $sheet->getStyle('A3')->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
        $this->tempfile = File::temporaryFileName();
        $writer = new OdsWriter($spreadsheetOld);
        $writer->useAdditionalNumberFormats([
            '¤#,##0.000' => $this->genericCurrencyWrite(...),
        ]);
        $writer->save($this->tempfile);
        $spreadsheetOld->disconnectWorksheets();
        $reader = new OdsReader();
        $reader->setFormatCallback($this->genericCurrencyRead(...));
        $spreadsheet = $reader->load($this->tempfile);
        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame('1.23', $newSheet->getCell('A1')->getFormattedValue());
        self::assertSame('¤2.340', $newSheet->getCell('A2')->getFormattedValue(), 'needs writer and reader callbacks');
        self::assertSame('$3.45 ', $newSheet->getCell('A3')->getFormattedValue());
        $spreadsheet->disconnectWorksheets();
    }

    private function genericCurrencyWrite(Style $obj, string $name): void
    {
        $writer = $obj->getWriter();
        $writer->startElement('number:currency-style');
        $writer->writeAttribute('style:name', $name);
        $writer->writeElement('number:text', '¤');
        $writer->startElement('number:number');
        $writer->writeAttribute('number:decimal-places', '3');
        $writer->writeAttribute('number:min-decimal-places', '3');
        $writer->writeAttribute('number:min-integer-digits', '1');
        $writer->endElement(); // number:number
        $writer->endElement(); // number:currency-style
    }

    private function genericCurrencyRead(string $type, string $text): string
    {
        $retVal = '';
        if ($type === 'float' && mb_substr($text, 0, 1) === '¤') {
            $retVal = '¤#,##0.000';
        }

        return $retVal;
    }
}
