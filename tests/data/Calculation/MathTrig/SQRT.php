<?php

return [
    ['exception'], // exception not enough args
    ['#VALUE!', 'ABC'],
    [0, 0],
    [0, null],
    [0, false],
    ['#VALUE!', ''],
    [1, true],
    [1.5, 2.25],
    [1.5, '2.25'],
    [1.5, '="2.25"'],
    [1.772454, M_PI],
    ['#NUM!', -2.1],
];
