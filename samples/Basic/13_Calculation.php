<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

mt_srand(1234567890);

require __DIR__ . '/../Header.php';

// List functions
$helper->log('List implemented functions');
$calc = Calculation::getInstance();
print_r($calc->getImplementedFunctionNames());

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Add some data, we will use some formulas here
$helper->log('Add some data and formulas');
$spreadsheet->getActiveSheet()->setCellValue('A14', 'Count:')
    ->setCellValue('A15', 'Sum:')
    ->setCellValue('A16', 'Max:')
    ->setCellValue('A17', 'Min:')
    ->setCellValue('A18', 'Average:')
    ->setCellValue('A19', 'Median:')
    ->setCellValue('A20', 'Mode:');

$spreadsheet->getActiveSheet()->setCellValue('A22', 'CountA:')
    ->setCellValue('A23', 'MaxA:')
    ->setCellValue('A24', 'MinA:');

$spreadsheet->getActiveSheet()->setCellValue('A26', 'StDev:')
    ->setCellValue('A27', 'StDevA:')
    ->setCellValue('A28', 'StDevP:')
    ->setCellValue('A29', 'StDevPA:');

$spreadsheet->getActiveSheet()->setCellValue('A31', 'DevSq:')
    ->setCellValue('A32', 'Var:')
    ->setCellValue('A33', 'VarA:')
    ->setCellValue('A34', 'VarP:')
    ->setCellValue('A35', 'VarPA:');

$spreadsheet->getActiveSheet()->setCellValue('A37', 'Date:');

$spreadsheet->getActiveSheet()->setCellValue('B1', 'Range 1')
    ->setCellValue('B2', 2)
    ->setCellValue('B3', 8)
    ->setCellValue('B4', 10)
    ->setCellValue('B5', true)
    ->setCellValue('B6', false)
    ->setCellValue('B7', 'Text String')
    ->setCellValue('B9', '22')
    ->setCellValue('B10', 4)
    ->setCellValue('B11', 6)
    ->setCellValue('B12', 12);

$spreadsheet->getActiveSheet()->setCellValue('B14', '=COUNT(B2:B12)')
    ->setCellValue('B15', '=SUM(B2:B12)')
    ->setCellValue('B16', '=MAX(B2:B12)')
    ->setCellValue('B17', '=MIN(B2:B12)')
    ->setCellValue('B18', '=AVERAGE(B2:B12)')
    ->setCellValue('B19', '=MEDIAN(B2:B12)')
    ->setCellValue('B20', '=MODE(B2:B12)');

$spreadsheet->getActiveSheet()->setCellValue('B22', '=COUNTA(B2:B12)')
    ->setCellValue('B23', '=MAXA(B2:B12)')
    ->setCellValue('B24', '=MINA(B2:B12)');

$spreadsheet->getActiveSheet()->setCellValue('B26', '=STDEV(B2:B12)')
    ->setCellValue('B27', '=STDEVA(B2:B12)')
    ->setCellValue('B28', '=STDEVP(B2:B12)')
    ->setCellValue('B29', '=STDEVPA(B2:B12)');

$spreadsheet->getActiveSheet()->setCellValue('B31', '=DEVSQ(B2:B12)')
    ->setCellValue('B32', '=VAR(B2:B12)')
    ->setCellValue('B33', '=VARA(B2:B12)')
    ->setCellValue('B34', '=VARP(B2:B12)')
    ->setCellValue('B35', '=VARPA(B2:B12)');

$spreadsheet->getActiveSheet()->setCellValue('B37', '=DATE(2007, 12, 21)')
    ->setCellValue('B38', '=DATEDIF( DATE(2007, 12, 21), DATE(2007, 12, 22), "D" )')
    ->setCellValue('B39', '=DATEVALUE("01-Feb-2006 10:06 AM")')
    ->setCellValue('B40', '=DAY( DATE(2006, 1, 2) )')
    ->setCellValue('B41', '=DAYS360( DATE(2002, 2, 3), DATE(2005, 5, 31) )');

$spreadsheet->getActiveSheet()->setCellValue('C1', 'Range 2')
    ->setCellValue('C2', 1)
    ->setCellValue('C3', 2)
    ->setCellValue('C4', 2)
    ->setCellValue('C5', 3)
    ->setCellValue('C6', 3)
    ->setCellValue('C7', 3)
    ->setCellValue('C8', '0')
    ->setCellValue('C9', 4)
    ->setCellValue('C10', 4)
    ->setCellValue('C11', 4)
    ->setCellValue('C12', 4);

$spreadsheet->getActiveSheet()->setCellValue('C14', '=COUNT(C2:C12)')
    ->setCellValue('C15', '=SUM(C2:C12)')
    ->setCellValue('C16', '=MAX(C2:C12)')
    ->setCellValue('C17', '=MIN(C2:C12)')
    ->setCellValue('C18', '=AVERAGE(C2:C12)')
    ->setCellValue('C19', '=MEDIAN(C2:C12)')
    ->setCellValue('C20', '=MODE(C2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('C22', '=COUNTA(C2:C12)')
    ->setCellValue('C23', '=MAXA(C2:C12)')
    ->setCellValue('C24', '=MINA(C2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('C26', '=STDEV(C2:C12)')
    ->setCellValue('C27', '=STDEVA(C2:C12)')
    ->setCellValue('C28', '=STDEVP(C2:C12)')
    ->setCellValue('C29', '=STDEVPA(C2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('C31', '=DEVSQ(C2:C12)')
    ->setCellValue('C32', '=VAR(C2:C12)')
    ->setCellValue('C33', '=VARA(C2:C12)')
    ->setCellValue('C34', '=VARP(C2:C12)')
    ->setCellValue('C35', '=VARPA(C2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('D1', 'Range 3')
    ->setCellValue('D2', 2)
    ->setCellValue('D3', 3)
    ->setCellValue('D4', 4);

$spreadsheet->getActiveSheet()->setCellValue('D14', '=((D2 * D3) + D4) & " should be 10"');

$spreadsheet->getActiveSheet()->setCellValue('E12', 'Other functions')
    ->setCellValue('E14', '=PI()')
    ->setCellValue('E15', '=RAND()')
    ->setCellValue('E16', '=RANDBETWEEN(5, 10)');

$spreadsheet->getActiveSheet()->setCellValue('E17', 'Count of both ranges:')
    ->setCellValue('F17', '=COUNT(B2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('E18', 'Total of both ranges:')
    ->setCellValue('F18', '=SUM(B2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('E19', 'Maximum of both ranges:')
    ->setCellValue('F19', '=MAX(B2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('E20', 'Minimum of both ranges:')
    ->setCellValue('F20', '=MIN(B2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('E21', 'Average of both ranges:')
    ->setCellValue('F21', '=AVERAGE(B2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('E22', 'Median of both ranges:')
    ->setCellValue('F22', '=MEDIAN(B2:C12)');

$spreadsheet->getActiveSheet()->setCellValue('E23', 'Mode of both ranges:')
    ->setCellValue('F23', '=MODE(B2:C12)');

// Calculated data
$helper->log('Calculated data');
for ($col = 'B'; $col != 'G'; ++$col) {
    for ($row = 14; $row <= 41; ++$row) {
        if (
            (($formula = $spreadsheet->getActiveSheet()->getCell($col . $row)->getValue()) !== null) &&
            ($formula[0] == '=')
        ) {
            $helper->log('Value of ' . $col . $row . ' [' . $formula . ']: ' . $spreadsheet->getActiveSheet()->getCell($col . $row)->getCalculatedValue());
        }
    }
}

//
//  If we set Pre Calculated Formulas to true then PhpSpreadsheet will calculate all formulae in the
//    workbook before saving. This adds time and memory overhead, and can cause some problems with formulae
//    using functions or features (such as array formulae) that aren't yet supported by the calculation engine
//  If the value is false (the default) for the Xlsx Writer, then MS Excel (or the application used to
//    open the file) will need to recalculate values itself to guarantee that the correct results are available.
//
//$writer->setPreCalculateFormulas(true);
// Save
$helper->write($spreadsheet, __FILE__);
