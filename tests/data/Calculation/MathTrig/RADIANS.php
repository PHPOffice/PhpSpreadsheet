<?php

declare(strict_types=1);

return [
    ['exception'], // exception not enough args
    ['#VALUE!', 'ABC'],
    [M_PI / 4, 45],
    [0, 0],
    [0, null],
    [0, false],
    ['#VALUE!', ''],
    ['#VALUE!', '=15+""'],
    [0, '=15+"-15"'],
    [0.017453293, true],
];
