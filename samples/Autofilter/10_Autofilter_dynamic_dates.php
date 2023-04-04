<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;

require __DIR__ . '/../Header.php';

// Sample can be slightly off if processing begins just before midnight
// and does not complete till after midnight.
// This possibility is accounted for in unit tests,
// but seems unneccesarily complicated for the sample.

function createSheet(Sample $helper, Spreadsheet $spreadsheet, string $rule, bool $displayInitialWorksheet): void
{
    $helper->log('Add data');

    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle($rule);
    $sheet->getCell('A1')->setValue('Date');
    $row = 1;
    $date = new DateTime();
    $year = (int) $date->format('Y');
    $month = (int) $date->format('m');
    $day = (int) $date->format('d');
    $yearMinus2 = $year - 2;
    $sheet->getCell('B1')->setValue("=DATE($year, $month, $day)");
    // Each day for two weeks before today through 2 weeks after
    for ($dayOffset = -14; $dayOffset < 14; ++$dayOffset) {
        ++$row;
        $sheet->getCell("A$row")->setValue("=B1+($dayOffset)");
    }
    // First and last day of each month, starting with January 2 years before,
    // through December 2 years after.
    for ($monthOffset = 0; $monthOffset < 48; ++$monthOffset) {
        ++$row;
        $sheet->getCell("A$row")->setValue("=DATE($yearMinus2, $monthOffset, 1)");
        ++$row;
        $sheet->getCell("A$row")->setValue("=DATE($yearMinus2, $monthOffset + 1, 0)");
    }
    $sheet->getStyle("A2:A$row")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
    $sheet->getStyle('B1')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);

    if ($displayInitialWorksheet) {
        $helper->log('Unfiltered Dates');
        $helper->displayGrid($sheet->toArray(null, true, true, true));
    }

    $helper->log("Filter for $rule");
    $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
    $autoFilter->setRange("A1:A{$row}");
    $columnFilter = $autoFilter->getColumn('A');
    $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
    $columnFilter->createRule()
        ->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, '', $rule)
        ->setRuleType(Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER);
    $sheet->setSelectedCell('B1');

    $helper->log('Execute filtering (apply the filter rules)');
    $autoFilter->showHideRows();

    $helper->log('Filtered Dates');
    $helper->displayGrid($sheet->toArray(null, true, true, true, true));
}

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Owen Leibman')
    ->setLastModifiedBy('Owen Leibman')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

$ruleNames = [
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_TODAY,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2,
    Rule::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_3,
];

// Create the worksheets
foreach ($ruleNames as $index => $ruleName) {
    createSheet($helper, $spreadsheet, $ruleName, $index === 0);
}
$spreadsheet->removeSheetByIndex(0);
$spreadsheet->setActiveSheetIndex(0);
// Save
$helper->write($spreadsheet, __FILE__);
