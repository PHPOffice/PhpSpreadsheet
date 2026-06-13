<?php

/**
 * Profile where time is spent in Xlsx::save() to identify real bottlenecks.
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require __DIR__ . '/../vendor/autoload.php';

$sheetCount = (int) ($argv[1] ?? 5);
$rowCount = (int) ($argv[2] ?? 5000);
$colCount = 10;

echo "Building {$sheetCount} sheets x {$rowCount} rows x {$colCount} cols...\n";
$spreadsheet = new Spreadsheet();
for ($s = 0; $s < $sheetCount; ++$s) {
    $sheet = ($s === 0) ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
    $sheet->setTitle("Sheet{$s}");
    for ($row = 1; $row <= $rowCount; ++$row) {
        for ($col = 1; $col <= $colCount; ++$col) {
            $sheet->setCellValue([$col, $row], "S{$s}R{$row}C{$col}");
        }
    }
}
echo "Done building.\n\n";

// Time individual components by using reflection/hooks
$writer = new Xlsx($spreadsheet);

// Time createStringTable
$t = microtime(true);
$ref = new ReflectionMethod($writer, 'createStringTable');
$ref->setAccessible(true);
$ref->invoke($writer);
$stringTableTime = microtime(true) - $t;

// Time createStyleDictionaries
$t = microtime(true);
$ref2 = new ReflectionMethod($writer, 'createStyleDictionaries');
$ref2->setAccessible(true);
$ref2->invoke($writer);
$styleTime = microtime(true) - $t;

// Time per-sheet writeWorksheet
$stringTableProp = new ReflectionProperty($writer, 'stringTable');
$stringTableProp->setAccessible(true);
$stringTable = $stringTableProp->getValue($writer);

$worksheetWriter = $writer->getWriterPartWorksheet();
$sheetTimes = [];
for ($i = 0; $i < $sheetCount; ++$i) {
    $t = microtime(true);
    /** @var array<string> $stringTable */
    $xml = $worksheetWriter->writeWorksheet($spreadsheet->getSheet($i), $stringTable, false);
    $sheetTimes[$i] = microtime(true) - $t;
}
$totalSheetTime = array_sum($sheetTimes);

// Time full save
$tempFile = tempnam(sys_get_temp_dir(), 'profile_');
$writer2 = new Xlsx($spreadsheet);
$t = microtime(true);
$writer2->save($tempFile);
$totalSaveTime = microtime(true) - $t;
@unlink($tempFile);

echo "=== Time Breakdown ===\n";
echo sprintf("createStringTable:      %.3fs  (%.1f%%)\n", $stringTableTime, $stringTableTime / $totalSaveTime * 100);
echo sprintf("createStyleDictionaries: %.3fs  (%.1f%%)\n", $styleTime, $styleTime / $totalSaveTime * 100);
echo sprintf("writeWorksheet (all):   %.3fs  (%.1f%%)\n", $totalSheetTime, $totalSheetTime / $totalSaveTime * 100);
for ($i = 0; $i < $sheetCount; ++$i) {
    echo sprintf("  sheet %d:              %.3fs\n", $i, $sheetTimes[$i]);
}
$otherTime = $totalSaveTime - $stringTableTime - $styleTime - $totalSheetTime;
echo sprintf("other (zip, rels, etc): %.3fs  (%.1f%%)\n", $otherTime, $otherTime / $totalSaveTime * 100);
echo sprintf("TOTAL save():           %.3fs\n", $totalSaveTime);

$spreadsheet->disconnectWorksheets();
