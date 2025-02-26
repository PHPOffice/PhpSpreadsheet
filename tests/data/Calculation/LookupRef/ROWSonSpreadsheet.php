<?php

declare(strict_types=1);

return [
    'global $E$2:$E$6' => [5, 'namedrangex'],
    'global $F$2:$H$2' => [1, 'namedrangey'],
    'global $F$4:$H$4' => [1, 'namedrange3'],
    'local in scope $F$5:$H$5' => [1, 'namedrange5'],
    'local out of scope' => ['#NAME?', 'localname'],
    'non-existent sheet' => [5, 'UnknownSheet!B2:K6'],
    'not enough arguments' => ['exception', 'omitted'],
    'other existing sheet' => [6, 'OtherSheet!A1:H6'],
    'qualified in scope $F$5:$H$5' => [1, 'ThisSheet!namedrange5'],
    'single cell absolute' => [1, '$C$7'],
    'single cell relative' => [1, 'C7'],
    'unknown name' => ['#NAME?', 'InvalidCellAddress'],
    'unknown name as first part of range' => ['#NAME?', 'Invalid:A2'],
    'unknown name as second part of range' => ['#NAME?', 'A2:Invalid'],
    //'qualified out of scope $F$6:$H$6' => [1, 'OtherSheet!localname'], // needs investigation
];
