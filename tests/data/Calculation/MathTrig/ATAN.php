<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"'],
    [0, 0],
    [0.785398, 1],
    [1.107149, 2],
    [-0.785398, -1],
    [1.570795, 1000000],
    // ['#NUM!', 2], Believe NAN is not possible
];
