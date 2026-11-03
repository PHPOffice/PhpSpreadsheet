<?php

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

// First build a small workbook that contains a pivot table, so this sample is
// self-contained. (In practice you would just load an existing .xlsx file.)
$helper->log('Create a workbook containing a pivot table');
$spreadsheet = new Spreadsheet();
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('Data');
$dataSheet->fromArray(
    [
        ['Region', 'Product', 'Amount'],
        ['East', 'Widget', 100],
        ['West', 'Widget', 150],
        ['East', 'Gadget', 200],
        ['West', 'Gadget', 250],
    ],
    null,
    'A1'
);
$pivotSheet = $spreadsheet->createSheet();
$pivotSheet->setTitle('PivotTable');

$builder = new PivotTableBuilder($dataSheet, 'A1:C5');
$builder
    ->addRowField('Region')
    ->addColumnField('Product')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);
$builder->build($pivotSheet, 'A3', 'SalesByRegion');

$tempFile = $helper->getTemporaryFilename('xlsx');
(new XlsxWriter($spreadsheet))->save($tempFile);
$spreadsheet->disconnectWorksheets();

// Now read the file back and inspect its pivot tables.
$helper->log('Load the workbook and inspect its pivot tables');
$loaded = (new XlsxReader())->load($tempFile);

foreach ($loaded->getWorksheetIterator() as $worksheet) {
    foreach ($worksheet->getPivotTableCollection() as $pivotTable) {
        $helper->log(sprintf(
            'Pivot table "%s" on sheet "%s" at %s',
            $pivotTable->getName(),
            $worksheet->getTitle(),
            $pivotTable->getLocation()
        ));

        $cache = $pivotTable->getCacheDefinition();
        if ($cache !== null) {
            $helper->log(sprintf(
                '  Source: %s!%s',
                (string) $cache->getSourceWorksheet(),
                (string) $cache->getSourceRange()
            ));
        }

        $helper->log('  Row fields:    ' . implode(', ', names($pivotTable->getRowFields())));
        $helper->log('  Column fields: ' . implode(', ', names($pivotTable->getColumnFields())));
        $helper->log('  Page fields:   ' . implode(', ', names($pivotTable->getPageFields())));

        foreach ($pivotTable->getDataFields() as $dataField) {
            $helper->log(sprintf(
                '  Value field:   %s (%s)',
                $dataField->getName(),
                (string) $dataField->getSubtotal()
            ));
        }
    }
}

$loaded->disconnectWorksheets();
@unlink($tempFile);

/**
 * @param PivotField[] $fields
 *
 * @return string[]
 */
function names(array $fields): array
{
    return array_map(static fn (PivotField $field): string => $field->getName(), $fields);
}
