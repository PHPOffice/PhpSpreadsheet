<?php

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;

require __DIR__ . '/../Header.php';

$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$helper->log('Create styles array');
$styles = [];
for ($i = 0; $i < 10; ++$i) {
    $style = new Style();
    $style->getFont()->setSize($i + 4);
    $styles[] = $style;
}

$helper->log('Add data (begin)');
$t = microtime(true);
for ($col = 1; $col <= 50; ++$col) {
    for ($row = 0; $row < 100; ++$row) {
        $str = ($row + $col);
        $style = $styles[$row % 10];
        $coord = Coordinate::stringFromColumnIndex($col) . ($row + 1);
        $worksheet->setCellValue($coord, $str);
        $worksheet->duplicateStyle($style, $coord);
    }
}
$d = microtime(true) - $t;
$helper->log('Add data (end) . time: ' . round($d . 2) . ' s');

// Save
$helper->write($spreadsheet, __FILE__);
