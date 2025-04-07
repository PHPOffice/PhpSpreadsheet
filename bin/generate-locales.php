#!/usr/bin/env php
<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheetInfra\LocaleGenerator;

require_once __DIR__ . '/..' . '/vendor/autoload.php';

$phpSpreadsheetFunctions = Calculation::getFunctions();

$localeGenerator = new LocaleGenerator(
    __DIR__ . '/../src/PhpSpreadsheet/Calculation/locale/',
    'Translations.xlsx',
    $phpSpreadsheetFunctions,
    true
);
$localeGenerator->generateLocales();
