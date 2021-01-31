<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"'],
    [0, 0],
    [1.472219, 0.9],
    [-1.472219, -0.9],
    ['#NUM!', 1],
    ['#NUM!', -1],
];
