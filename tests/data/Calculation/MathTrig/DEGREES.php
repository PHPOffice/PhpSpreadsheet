<?php

return [
    ['exception'], // exception not enough args
    ['#VALUE!', 'ABC'],
    [45, M_PI / 4],
    [0, 0],
    [0, null],
    [0, false],
    ['#VALUE!', ''],
    [57.29577951, true],
];
