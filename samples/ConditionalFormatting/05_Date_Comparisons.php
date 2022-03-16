<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Mark Baker')
    ->setLastModifiedBy('Mark Baker')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Create the worksheet
$helper->log('Add data');

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('B1', 'yesterday()')
    ->setCellValue('C1', 'today()')
    ->setCellValue('D1', 'tomorrow()')
    ->setCellValue('E1', 'last7Days()')
    ->setCellValue('F1', 'lastWeek()')
    ->setCellValue('G1', 'thisWeek()')
    ->setCellValue('H1', 'nextWeek()')
    ->setCellValue('I1', 'lastMonth()')
    ->setCellValue('J1', 'thisMonth()')
    ->setCellValue('K1', 'nextMonth()');

$dateFunctionArray = [
    'yesterday()',
    'today()',
    'tomorrow()',
    'last7Days()',
    'lastWeek()',
    'thisWeek()',
    'nextWeek()',
    'lastMonth()',
    'thisMonth()',
    'nextMonth()',
];
$dateTitleArray = [
    ['First day of last month'],
    ['Last day of last month'],
    ['Last Monday'],
    ['Last Friday'],
    ['Monday last week'],
    ['Wednesday last week'],
    ['Friday last week'],
    ['Yesterday'],
    ['Today'],
    ['Tomorrow'],
    ['Monday this week'],
    ['Wednesday this week'],
    ['Friday this week'],
    ['Monday next week'],
    ['Wednesday next week'],
    ['Friday next week'],
    ['First day of next month'],
    ['Last day of next month'],
];
$dataArray = [
    ['=EOMONTH(TODAY(),-2)+1'],
    ['=EOMONTH(TODAY(),-1)'],
    ['=TODAY()-WEEKDAY(TODAY(),3)'],
    ['=TODAY()-WEEKDAY(TODAY())-1'],
    ['=2-WEEKDAY(TODAY())+TODAY()-7'],
    ['=4-WEEKDAY(TODAY())+TODAY()-7'],
    ['=6-WEEKDAY(TODAY())+TODAY()-7'],
    ['=TODAY()-1'],
    ['=TODAY()'],
    ['=TODAY()+1'],
    ['=2-WEEKDAY(TODAY())+TODAY()'],
    ['=4-WEEKDAY(TODAY())+TODAY()'],
    ['=6-WEEKDAY(TODAY())+TODAY()'],
    ['=2-WEEKDAY(TODAY())+TODAY()+7'],
    ['=4-WEEKDAY(TODAY())+TODAY()+7'],
    ['=6-WEEKDAY(TODAY())+TODAY()+7'],
    ['=EOMONTH(TODAY(),0)+1'],
    ['=EOMONTH(TODAY(),1)'],
];

$spreadsheet->getActiveSheet()
    ->fromArray($dateFunctionArray, null, 'B1', true);
$spreadsheet->getActiveSheet()
    ->fromArray($dateTitleArray, null, 'A2', true);
for ($column = 'B'; $column !== 'L'; ++$column) {
    $spreadsheet->getActiveSheet()
        ->fromArray($dataArray, null, "{$column}2", true);
}

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('B1:K1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('B1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// Define some styles for our Conditionals
$helper->log('Define some styles for our Conditionals');
$yellowStyle = new Style(false, true);
$yellowStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getEndColor()->setARGB(Color::COLOR_YELLOW);
$yellowStyle->getFont()->setColor(new Color(Color::COLOR_BLUE));

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting and set styles');
for ($column = 'B'; $column !== 'L'; ++$column) {
    $wizardFactory = new Wizard("{$column}2:{$column}19");
    /** @var Wizard\DateValue $dateWizard */
    $dateWizard = $wizardFactory->newRule(Wizard::DATES_OCCURRING);
    $conditionalStyles = [];

    $methodName = trim($spreadsheet->getActiveSheet()->getCell("{$column}1")->getValue(), '()');
    $dateWizard->$methodName()
        ->setStyle($yellowStyle);

    $conditionalStyles[] = $dateWizard->getConditional();

    $spreadsheet->getActiveSheet()
        ->getStyle($dateWizard->getCellRange())
        ->setConditionalStyles($conditionalStyles);
}

// Set conditional formatting rules and styles
$helper->log('Set some additional styling for date formats');

$spreadsheet->getActiveSheet()->getStyle('B:B')->getNumberFormat()->setFormatCode('ddd dd-mmm-yyyy');
for ($column = 'A'; $column !== 'L'; ++$column) {
    if ($column !== 'A') {
        $spreadsheet->getActiveSheet()->getStyle("{$column}:{$column}")
            ->getNumberFormat()->setFormatCode('ddd dd-mmm-yyyy');
    }
    $spreadsheet->getActiveSheet()->getColumnDimension($column)
        ->setAutoSize(true);
}
$spreadsheet->getActiveSheet()->getStyle('A:A')->getFont()->setBold(true);

// Save
$helper->write($spreadsheet, __FILE__);
