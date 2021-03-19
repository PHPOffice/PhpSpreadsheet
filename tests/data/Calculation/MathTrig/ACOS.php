<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"'],
    [1.570796, 0],
    [3.141593, -1],
    [0, 1],
    ['#NUM!', 2],
];
