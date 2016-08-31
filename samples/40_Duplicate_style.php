<?php

require __DIR__ . '/Header.php';

$helper->log('Create new Spreadsheet object');
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$helper->log('Create styles array');
$styles = [];
for ($i = 0; $i < 10; ++$i) {
    $style = new \PhpOffice\PhpSpreadsheet\Style();
    $style->getFont()->setSize($i + 4);
    $styles[] = $style;
}

$helper->log('Add data (begin)');
$t = microtime(true);
for ($col = 0; $col < 50; ++$col) {
    for ($row = 0; $row < 100; ++$row) {
        $str = ($row + $col);
        $style = $styles[$row % 10];
        $coord = \PhpOffice\PhpSpreadsheet\Cell::stringFromColumnIndex($col) . ($row + 1);
        $worksheet->setCellValue($coord, $str);
        $worksheet->duplicateStyle($style, $coord);
    }
}
$d = microtime(true) - $t;
$helper->log('Add data (end), time: ' . round($d, 2) . ' s');

// Save
$helper->write($spreadsheet, __FILE__);
