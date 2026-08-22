<?php

declare(strict_types=1);

return [
    ['exception'], // exception not enough args
    ['#VALUE!', 'ABC'],
    ['#NUM!', 0],
    ['#NUM!', -1],
    [-2.302585, 0.1],
    [0, 1],
    [2.302585, 10],
];
