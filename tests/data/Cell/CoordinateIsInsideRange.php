<?php

declare(strict_types=1);

return [
    [true, 'A1:E20', 'B4'],
    [false, 'A1:E20', 'F36'],
    [true, '$A$1:$E$20', '$B$4'],
    [false, '$A$1:$E$20', '$F$36'],
    [true, '$A$1:$E$20', 'B4'],
    [false, '$A$1:$E$20', 'F36'],
    [true, 'A1:E20', '$B$4'],
    [false, 'A1:E20', '$F$36'],
    [true, 'Sheet!A1:E20', 'Sheet!B4'],
    'case insensitive' => [true, 'Sheet!A1:E20', 'sheet!B4'],
    'apostrophes 1st sheetname not 2nd' => [true, '\'Sheet\'!A1:E20', 'sheet!B4'],
    'apostrophes 2nd sheetname not 1st' => [true, 'Sheet!A1:E20', '\'sheet\'!B4'],
    [false, 'Sheet!A1:E20', 'Sheet!F36'],
    [true, 'Sheet!$A$1:$E$20', 'Sheet!$B$4'],
    [false, 'Sheet!$A$1:$E$20', 'Sheet!$F$36'],
    [false, 'Sheet!A1:E20', 'B4'],
    [false, 'Sheet!A1:E20', 'F36'],
    [false, 'Sheet!$A$1:$E$20', '$B$4'],
    [false, 'Sheet!$A$1:$E$20', '$F$36'],
    [false, 'A1:E20', 'Sheet!B4'],
    [false, 'A1:E20', 'Sheet!F36'],
    [false, '$A$1:$E$20', 'Sheet!$B$4'],
    [false, '$A$1:$E$20', 'Sheet!$F$36'],
    [true, '\'Sheet space\'!A1:E20', '\'Sheet space\'!B4'],
    [false, '\'Sheet space\'!A1:E20', '\'Sheet space\'!F36'],
    [true, '\'Sheet space\'!$A$1:$E$20', '\'Sheet space\'!$B$4'],
    [false, '\'Sheet space\'!$A$1:$E$20', '\'Sheet space\'!$F$36'],
];
