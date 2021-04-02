<?php

return [
    ['#VALUE!', 'y', 3],
    ['#VALUE!', 3, 'y'],
    ['#NUM!', 3, -3],
    ['#NUM!', 30, 10],
    [0, '20', 30],
    [0, 20, '30'],
    [0, 20, 30],
    [0, null, 30],
    [0, false, 30],
    [0, true, 30],
    [0, -30, true],
    [0, -30, false],
    [0, -30, null],
    ['exception', -30],
    ['exception'],
];
