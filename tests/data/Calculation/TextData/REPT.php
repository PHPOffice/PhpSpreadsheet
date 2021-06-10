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
];
