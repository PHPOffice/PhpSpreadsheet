<?php

use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

require_once __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();

$data2019 = [
    [151600, 21600],
    [160320, 30320],
    [243500, 73500],
    [113450, 13450],
    [143200, 23200],
    [134000, 14000],
    [89400, -10600],
    [184500, 24500],
    [100800, 800],
    [241850, 5150],
    [142425, 12425],
    [243400, 43400],
];

$data2020 = [
    [183250, 33250],
    [210350, 40350],
    [298650, 48650],
    [140550, 20550],
    [183145, 33145],
    [172355, 22355],
];

$worksheet = $spreadsheet->setActiveSheetIndex(0);
setYearlyData($worksheet, '2019', $data2019);
$worksheet = $spreadsheet->addSheet(new Worksheet($spreadsheet));
setYearlyData($worksheet, '2020', $data2020);
$worksheet = $spreadsheet->addSheet(new Worksheet($spreadsheet));
setYearlyData($worksheet, '2020', [], 'GROWTH');

function setYearlyData(Worksheet $worksheet, string $year, array $yearlyData, ?string $title = null): void
{
    // Set up some basic data
    $worksheetTitle = $title ?: $year;
    $worksheet
        ->setTitle($worksheetTitle)
        ->setCellValue('A1', 'Month')
        ->setCellValue('B1', $worksheetTitle === 'GROWTH' ? 'Growth' : 'Sales')
        ->setCellValue('C1', $worksheetTitle === 'GROWTH' ? 'Profit Growth' : 'Margin')
        ->setCellValue('A2', Date::stringToExcel("{$year}-01-01"));
    for ($row = 3; $row <= 13; ++$row) {
        $worksheet->setCellValue("A{$row}", '=NEXT_MONTH');
    }

    if (!empty($yearlyData)) {
        $worksheet->fromArray($yearlyData, null, 'B2');
    } else {
        for ($row = 2; $row <= 13; ++$row) {
            $worksheet->setCellValue("B{$row}", '=GROWTH');
            $worksheet->setCellValue("C{$row}", '=PROFIT_GROWTH');
        }
    }

    $worksheet->getStyle('A1:C1')
        ->getFont()->setBold(true);
    $worksheet->getStyle('A2:A13')
        ->getNumberFormat()
        ->setFormatCode('mmmm');
    $worksheet->getStyle('B2:C13')
        ->getNumberFormat()
        ->setFormatCode($worksheetTitle === 'GROWTH' ? '0.00%' : '_-€* #,##0_-');
}

// Add some Named Formulae
// The first to store our tax rate
$spreadsheet->addNamedFormula(new NamedFormula('NEXT_MONTH', $worksheet, '=EDATE(OFFSET($A1,-1,0),1)'));
$spreadsheet->addNamedFormula(new NamedFormula('GROWTH', $worksheet, "=IF('2020'!\$B1=\"\",\"-\",(('2020'!\$B1/'2019'!\$B1)-1))"));
$spreadsheet->addNamedFormula(new NamedFormula('PROFIT_GROWTH', $worksheet, "=IF('2020'!\$C1=\"\",\"-\",(('2020'!\$C1/'2019'!\$C1)-1))"));

for ($row = 2; $row <= 7; ++$row) {
    $month = $worksheet->getCell("A{$row}")->getFormattedValue();
    $growth = $worksheet->getCell("B{$row}")->getFormattedValue();
    $profitGrowth = $worksheet->getCell("C{$row}")->getFormattedValue();

    $helper->log("Growth for {$month} is {$growth}, with a Profit Growth of {$profitGrowth}");
}

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
