<?php

return [
    'no arguments' => ['exception'],
    'one argument' => ['exception', 'ABC'],
    ['#VALUE!', 'ABC', 'DEF'],
    ['ABCABCABC', 'ABC', 3],
    ['ABCABC', 'ABC', 2.2],
    ['', 'ABC', 0],
    ['TRUETRUE', true, 2],
    ['111', 1, 3],
    ['δύο δύο ', 'δύο ', 2],
    ['#VALUE!', 'ABC', -1],
    'result too long' => ['#VALUE!', 'A', 32768],
    'result just fits' => [str_repeat('A', 32767), 'A', 32767],
    'propagate NUM' => ['#NUM!', '=SQRT(-1)', 5],
    'propagate REF' => ['#REF!', '=sheet99!A1', 5],
];
