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
            $endDays = date('t', mktime(0, 0, 0, $period, 1, $year));
            for ($i = 1; $i <= $endDays; ++$i) {
                $eDate = Date::formattedPHPToExcel(
                    $year,
                    $period,
                    $i
                );
                $value = rand(500, 1000) * (1 + rand(-0.25, +0.25));
                $salesValue = $invoiceValue = null;
                $incomeOrExpenditure = rand(-1, 1);
                if ($incomeOrExpenditure == -1) {
                    $expenditure = rand(-500, -1000) * (1 + rand(-0.25, +0.25));
                    $income = null;
                } elseif ($incomeOrExpenditure == 1) {
                    $expenditure = rand(-500, -1000) * (1 + rand(-0.25, +0.25));
                    $income = rand(500, 1000) * (1 + rand(-0.25, +0.25));
                } else {
                    $expenditure = null;
                    $income = rand(500, 1000) * (1 + rand(-0.25, +0.25));
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
// Filter the Country column on a filter value of Germany
//	As it's just a simple value filter, we can use FILTERTYPE_FILTER
$autoFilter->getColumn('C')
    ->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER)
    ->createRule()
    ->setRule(
        Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
        'Germany'
    );
// Filter the Date column on a filter value of the year to date
$autoFilter->getColumn('D')
    ->setFilterType(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER)
    ->createRule()
    ->setRule(
        Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
        null,
        Rule::AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE
    )
    ->setRuleType(Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER);
// Display only sales values that are between 400 and 600
$autoFilter->getColumn('E')
    ->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
    ->createRule()
    ->setRule(
        Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL,
        400
    )
    ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
$autoFilter->getColumn('E')
    ->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND)
    ->createRule()
    ->setRule(
        Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL,
        600
    )
    ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

// Save
$helper->write($spreadsheet, __FILE__);
