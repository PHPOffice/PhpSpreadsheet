#!/usr/bin/env php
<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheetInfra\DocumentGenerator;

require_once __DIR__ . '/..' . '/vendor/autoload.php';

$phpSpreadsheetFunctions = Calculation::getFunctions();
ksort($phpSpreadsheetFunctions);

file_put_contents(
    __DIR__ . '/../docs/references/function-list-by-category.md',
    DocumentGenerator::generateFunctionListByCategory($phpSpreadsheetFunctions)
);
file_put_contents(
    __DIR__ . '/../docs/references/function-list-by-name.md',
    DocumentGenerator::generateFunctionListByName($phpSpreadsheetFunctions)
);
