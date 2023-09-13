<?php

declare(strict_types=1);

return [
    'current row' => [3, 'omitted'],
    'global name $E$2:$E$6' => [2, 'namedrangex'],
    'global name $F$2:$H$2' => [2, 'namedrangey'],
    'global name $F$4:$H$4' => [4, 'namedrange3'],
    'local name $F$5:$H$5' => [5, 'namedrange5'],
    'out of scope name' => ['#NAME?', 'localname'],
    'qualified cell existing sheet' => [1, 'OtherSheet!A1'],
    'qualified cell non-existent sheet' => [1, 'UnknownSheet!A1'],
    'single cell absolute' => [7, '$C$7'],
    'single cell relative' => [7, 'C7'],
    'unknown name' => ['#NAME?', 'namedrange2'],
    'unknown name as first part of range' => ['#NAME?', 'InvalidCell:A2'],
    'unknown name as second part of range' => ['#NAME?', 'A2:InvalidCell'],
    //'qualified name' => [6, 'OtherSheet!localname'], // Never reaches function
];
