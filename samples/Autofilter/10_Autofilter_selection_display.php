<?php

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Create the worksheet
$helper->log('Add data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Financial Year')
    ->setCellValue('B1', 'Financial Period')
    ->setCellValue('C1', 'Country')
    ->setCellValue('D1', 'Date')
    ->setCellValue('E1', 'Sales Value')
    ->setCellValue('F1', 'Expenditure');
$startYear = $endYear = $currentYear = date('Y');
--$startYear;
++$endYear;

$years = range($startYear, $endYear);
$periods = range(1, 12);
$countries = [
    'United States',
    'UK',
    'France',
    'Germany',
    'Italy',
    'Spain',
    'Portugal',
    'Japan',
];

$row = 2;
foreach ($years as $year) {
    foreach ($periods as $period) {
        foreach ($countries as $country) {
            $endDays = date('t', mktime(0, 0, 0, $period, 1, (int) $year));
            for ($i = 1; $i <= $endDays; ++$i) {
                $eDate = Date::formattedPHPToExcel(
                    $year,
                    $period,
                    $i
                );
                $value = rand(500, 1000) * (1 + (rand(-1, 1) / 4));
                $salesValue = $invoiceValue = null;
                $incomeOrExpenditure = rand(-1, 1);
                if ($incomeOrExpenditure == -1) {
                    $expenditure = rand(-500, -1000) * (1 + (rand(-1, 1) / 4));
                    $income = null;
                } elseif ($incomeOrExpenditure == 1) {
                    $expenditure = rand(-500, -1000) * (1 + (rand(-1, 1) / 4));
                    $income = rand(500, 1000) * (1 + (rand(-1, 1) / 4));
                } else {
                    $expenditure = null;
                    $income = rand(500, 1000) * (1 + (rand(-1, 1) / 4));
                }
                $dataArray = [$year,
                    $period,
                    $country,
                    $eDate,
                    $income,
                    $expenditure,
                ];
                $spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A' . $row++);
            }
        }
    }
}
--$row;

// Set styling
$helper->log('Set styling');
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12.5);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10.5);
$spreadsheet->getActiveSheet()->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);
$spreadsheet->getActiveSheet()->getStyle('E2:F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$spreadsheet->getActiveSheet()->freezePane('A2');

// Set autofilter range
$helper->log('Set autofilter range');
// Always include the complete filter range!
// Excel does support setting only the caption
// row, but that's not a best practise...
$spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());

// Set active filters
$autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
$helper->log('Set active filters');
// Filter the Country column on a filter value of countries beginning with the letter U (or Japan)
//     We use * as a wildcard, so specify as U* and using a wildcard requires customFilter
$autoFilter->getColumn('C')
    ->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
    ->createRule()
    ->setRule(
        Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
        'u*'
    )
    ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
$autoFilter->getColumn('C')
    ->createRule()
    ->setRule(
        Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
        'japan'
    )
    ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
// Filter the Date column on a filter value of the first day of every period of the current year
// We us a dateGroup ruletype for this, although it is still a standard filter
foreach ($periods as $period) {
    $endDate = date('t', mktime(0, 0, 0, $period, 1, (int) $currentYear));

    $autoFilter->getColumn('D')
        ->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER)
        ->createRule()
        ->setRule(
            Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
            [
                'year' => $currentYear,
                'month' => $period,
                'day' => $endDate,
            ]
        )
        ->setRuleType(Rule::AUTOFILTER_RULETYPE_DATEGROUP);
}
// Display only sales values that are blank
//     Standard filter, operator equals, and value of NULL
$autoFilter->getColumn('E')
    ->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER)
    ->createRule()
    ->setRule(
        Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
        ''
    );

// Execute filtering
$helper->log('Execute filtering');
$autoFilter->showHideRows();

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Display Results of filtering
$helper->log('Display filtered rows');
foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row) {
    if ($spreadsheet->getActiveSheet()->getRowDimension($row->getRowIndex())->getVisible()) {
        $helper->log('    Row number - ' . $row->getRowIndex());
        $helper->log($spreadsheet->getActiveSheet()->getCell('C' . $row->getRowIndex())->getValue());
        $helper->log($spreadsheet->getActiveSheet()->getCell('D' . $row->getRowIndex())->getFormattedValue());
    }
}
