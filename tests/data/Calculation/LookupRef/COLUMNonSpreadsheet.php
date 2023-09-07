<?php

declare(strict_types=1);

return [
    'current column' => [2, 'omitted'],
    'global name $E$2:$E$6' => [5, 'namedrangex'],
    'global name $F$2:$H$2' => [6, 'namedrange3'],
    'global name $F$4:$H$4' => [6, 'namedrangey'],
    'out of scope name' => ['#NAME?', 'localname'],
    'qualified cell existing sheet' => [1, 'OtherSheet!A1'],
    'qualified cell non-existent sheet' => [1, 'UnknownSheet!A1'],
    'single cell absolute' => [3, '$C$15'],
    'single cell relative' => [3, 'C7'],
    'unknown name' => ['#NAME?', 'namedrange2'],
    'unknown name as first part of range' => ['#NAME?', 'Invalid:A2'],
    'unknown name as second part of range' => ['#NAME?', 'A2:Invalid'],
    //'qualified name' => [6, 'OtherSheet!localname'], // Never reaches function
];
