<?php

return [
    ['exception'], // exception not enough args
    ['#VALUE!', 'ABC'],
    ['#NUM!', 0],
    ['#NUM!', -1],
    [-1, 0.1],
    [0, 1],
    [0.301030, 2],
    [1, 10],
];
