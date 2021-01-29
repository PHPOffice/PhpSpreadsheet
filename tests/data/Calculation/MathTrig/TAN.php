<?php

return [
    ['#VALUE!'], // exception not enough args
    ['#VALUE!', '"ABC"'],
    [0, 0],
    [1.557408, 1],
    [-2.185040, 2],
    [1, M_PI / 4],
    ['#DIV/0!', M_PI_2],
    ['#DIV/0!', -M_PI_2],
    ['#DIV/0!', 3 * M_PI_2],
];
