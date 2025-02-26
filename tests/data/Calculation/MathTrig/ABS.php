<?php

declare(strict_types=1);

return [
    ['exception'], // exception - not enough args
    ['#VALUE!', 'ABC'],
    [35.51, '35.51'],
    [35.51, '=35.51'],
    [35.51, '="35.51"'],
    [35.51, 35.51],
    [35.51, -35.51],
    [6, '6'],
    [7, '-7'],
    [0, 0],
    [0, null],
    [0, false],
    [1, true],
    ['#VALUE!', ''],
];
