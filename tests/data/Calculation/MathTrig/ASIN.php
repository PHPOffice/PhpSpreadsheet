<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"'],
    [1.570796, 1],
    [-1.570796, -1],
    [0, 0],
    ['#NUM!', 2],
];
