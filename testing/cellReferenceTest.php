<?php

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('UTC');

// Adjust the path as required to reference the PHPSpreadsheet Bootstrap file
require_once __DIR__ . '/../vendor/autoload.php';

$cellRange1 = Coordinate::extractAllCellReferencesInRange('D3');
var_dump($cellRange1);
$cellRange2 = Coordinate::extractAllCellReferencesInRange('D3:E4');
var_dump($cellRange2);
$cellRange3 = Coordinate::extractAllCellReferencesInRange('Sheet1!D3');
var_dump($cellRange3);
$cellRange4 = Coordinate::extractAllCellReferencesInRange('Sheet1!D3:E4');
var_dump($cellRange4);

$cellRange11 = Coordinate::extractAllCellReferencesInRange('D3:E4 D4:F6');
var_dump($cellRange11);
$cellRange12 = Coordinate::extractAllCellReferencesInRange('D3:E4,D4:F6');
var_dump($cellRange12);

$cellRange5 = Coordinate::extractAllCellReferencesInRange('Sheet1!D3:Sheet2!E4');
var_dump($cellRange5);
