<?php

return [
    'global $e$2-$e$6' => [1, 'namedrangex'],
    'global $f$2:$h$2' => [3, 'namedrangey'],
    'global $f$4:$h$4' => [3, 'namedrange3'],
    'local in scope $f$5:$i$5' => [4, 'namedrange5'],
    'local out of scope' => ['#NAME?', 'localname'],
    'non-existent sheet' => [10, 'UnknownSheet!B2:K6'],
    'not enough arguments' => ['exception', 'omitted'],
    'other existing sheet' => [6, 'OtherSheet!B1:G1'],
    'qualified in scope $f$5:$i$5' => [4, 'ThisSheet!namedrange5'],
    'single cell absolute' => [1, '$C$15'],
    'single cell relative' => [1, 'C7'],
    'unknown name' => ['#NAME?', 'namedrange2'],
    'unknown name as first part of range' => ['#NAME?', 'Invalid:A2'],
    'unknown name as second part of range' => ['#NAME?', 'A2:Invalid'],
    //'qualified out of scope $f$6:$h$6' => [3, 'OtherSheet!localname'], // needs investigation
];
