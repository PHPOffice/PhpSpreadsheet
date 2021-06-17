<?php

return [
    ['exception'], // exception not enough args
    ['#VALUE!', 'ABC'],
    [2.7182818284590, 1],
    [2.7182818284590, true],
    [1, false],
    [1, null],
    ['#VALUE!', ''],
];
