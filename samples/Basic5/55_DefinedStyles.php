<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Style;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$array = [
    ['NUMBER', 1, NumberFormat::FORMAT_NUMBER],
    ['NUMBER_0', 1, NumberFormat::FORMAT_NUMBER_0],
    ['NUMBER_00', 1, NumberFormat::FORMAT_NUMBER_00],
    ['NUMBER_COMMA_SEPARATED1', 1234, NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1],
    ['NUMBER_COMMA_SEPARATED2', 1234, NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2],
    ['PERCENTAGE', 0.98, NumberFormat::FORMAT_PERCENTAGE],
    ['PERCENTAGE_0', 0.985, NumberFormat::FORMAT_PERCENTAGE_0],
    ['PERCENTAGE_00', 0.9856, NumberFormat::FORMAT_PERCENTAGE_00],
    ['DATE_YYYYMMDD', 46000, NumberFormat::FORMAT_DATE_YYYYMMDD],
    ['DATE_DDMMYYYY', 46000, NumberFormat::FORMAT_DATE_DDMMYYYY],
    ['DATE_DMYSLASH', 46000, NumberFormat::FORMAT_DATE_DMYSLASH],
    ['DATE_DMYMINUS', 46000, NumberFormat::FORMAT_DATE_DMYMINUS],
    ['DATE_DMMINUS', 46000, NumberFormat::FORMAT_DATE_DMMINUS],
    ['DATE_MYMINUS', 46000, NumberFormat::FORMAT_DATE_MYMINUS],
    ['DATE_XLSX14', 46000, NumberFormat::FORMAT_DATE_XLSX14],
    ['DATE_XLSX14_ACTUAL', 46000, NumberFormat::FORMAT_DATE_XLSX14_ACTUAL],
    ['DATE_XLSX15', 46000, NumberFormat::FORMAT_DATE_XLSX15],
    ['DATE_XLSX15_YYYY', 46000, NumberFormat::FORMAT_DATE_XLSX15_YYYY],
    ['DATE_XLSX16', 46000, NumberFormat::FORMAT_DATE_XLSX16],
    ['DATE_XLSX17', 46000, NumberFormat::FORMAT_DATE_XLSX17],
    ['DATE_XLSX22', 46000, NumberFormat::FORMAT_DATE_XLSX22],
    ['DATE_XLSX22_ACTUAL', 46000, NumberFormat::FORMAT_DATE_XLSX22_ACTUAL],
    ['DATE_DATETIME', 46000.25, NumberFormat::FORMAT_DATE_DATETIME],
    ['DATE_DATETIME_BETTER', 46000.25, NumberFormat::FORMAT_DATE_DATETIME_BETTER],
    ['DATE_TIME1', 46000.25, NumberFormat::FORMAT_DATE_TIME1],
    ['DATE_TIME2', 46000.25, NumberFormat::FORMAT_DATE_TIME2],
    ['DATE_TIME3', 46000.25, NumberFormat::FORMAT_DATE_TIME3],
    ['DATE_TIME4', 46000.25, NumberFormat::FORMAT_DATE_TIME4],
    ['DATE_TIME5', 46000.25, NumberFormat::FORMAT_DATE_TIME5],
    ['DATE_TIME6', 46000.25, NumberFormat::FORMAT_DATE_TIME6],
    ['DATE_TIME7', 46000.25, NumberFormat::FORMAT_DATE_TIME7],
    ['DATE_TIME8', 46000.25, NumberFormat::FORMAT_DATE_TIME8],
    [' DATE_TIME_INTERVAL_HMS', 0.0087731481481482, NumberFormat::FORMAT_DATE_TIME_INTERVAL_HMS],
    ['DATE_YYYYMMDDSLASH', 46000, NumberFormat::FORMAT_DATE_YYYYMMDDSLASH],
    ['DATE_LONG_DATE', 46000, NumberFormat::FORMAT_DATE_LONG_DATE],
    ['CURRENCY_USD_INTEGER', 1234.56, NumberFormat::FORMAT_CURRENCY_USD_INTEGER],
    ['CURRENCY_USD', 1234.56, NumberFormat::FORMAT_CURRENCY_USD],
    ['CURRENCY_EUR_INTEGER', 1234.56, NumberFormat::FORMAT_CURRENCY_EUR_INTEGER],
    ['CURRENCY_EUR', 1234.56, NumberFormat::FORMAT_CURRENCY_EUR],
    ['ACCOUNTING_USD', 1234.56, NumberFormat::FORMAT_CURRENCY_USD],
    ['ACCOUNTING_EUR', 1234.56, NumberFormat::FORMAT_CURRENCY_EUR],
    ['CUSTOM1', 1234.56, '0.000'],
    ['CUSTOM2', 1234.56, '"$"#,##0.00_);[Red]\("$"#,##0.00\)'],
];
$row = 0;
$helper->log('Populate spreadsheet');
foreach ($array as $cells) {
    ++$row;
    $sheet->getCell("A$row")->setValue($cells[0]);
    $sheet->getCell("B$row")->setValue($cells[1]);
    if (!str_starts_with($cells[0], 'DATE')) {
        $sheet->getCell("C$row")->setValue(-$cells[1]);
    }
    $sheet->getStyle("B$row:C$row")
        ->getNumberFormat()
        ->setFormatCode($cells[2]);
}
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->setSelectedCells('A1');

function threeDecimalPlaces(Style $obj, string $name): void
{
    $writer = $obj->getWriter();
    $writer->startElement('number:number-style');
    $writer->writeAttribute('style:name', $name);
    $writer->startElement('number:number');
    $writer->writeAttribute('number:decimal-places', '3');
    $writer->writeAttribute('number:min-decimal-places', '3');
    $writer->writeAttribute('number:min-integer-digits', '1');
    $writer->endElement(); // number:number
    $writer->endElement(); // number:number-style
}

function redBrackets(Style $obj, string $name): void
{
    $writer = $obj->getWriter();
    $writer->startElement('number:currency-style');
    $writer->writeAttribute('style:name', $name . 'P0');
    $writer->writeElement('number:currency-symbol', '$');
    $writer->startElement('number:number');
    $writer->writeAttribute('number:decimal-places', '2');
    $writer->writeAttribute('number:min-decimal-places', '2');
    $writer->writeAttribute('number:min-integer-digits', '1');
    $writer->writeAttribute('number:grouping', 'true');
    $writer->endElement(); // number:number
    $writer->writeElement('number:text', ' ');
    $writer->endElement(); // number:currency-style

    $writer->startElement('number:currency-style');
    $writer->writeAttribute('style:name', $name);
    $writer->startElement('style:text-properties');
    $writer->writeAttribute('fo:color', '#FF0000');
    $writer->endElement(); // style:text-properties
    $writer->writeElement('number:text', '(');
    $writer->startElement('number:number');
    $writer->writeAttribute('number:decimal-places', '2');
    $writer->writeAttribute('number:min-decimal-places', '2');
    $writer->writeAttribute('number:min-integer-digits', '1');
    $writer->writeAttribute('number:grouping', 'true');
    $writer->endElement(); // number:number
    $writer->writeElement('number:text', ')');
    $writer->startElement('style:map');
    $writer->writeAttribute('style:condition', 'value()>=0');
    $writer->writeAttribute('style:apply-style-name', $name . 'P0');
    $writer->endElement(); // style:map
    $writer->endElement(); // number:currency-style
}

function writeAdditional(BaseWriter $writer): void
{
    if (method_exists($writer, 'useAdditionalNumberFormats')) {
        $array = [
            '0.000' => threeDecimalPlaces(...),
            '"$"#,##0.00_);[Red]\("$"#,##0.00\)' => redBrackets(...),
        ];
        $writer->useAdditionalNumberFormats($array);
    }
}

$helper->write($spreadsheet, __FILE__, ['Xlsx', 'Ods'], writerCallback: writeAdditional(...));
$spreadsheet->disconnectWorksheets();
