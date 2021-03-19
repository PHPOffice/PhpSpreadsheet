<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"'],
    [0, 0],
    [0.881374, 1],
    [1.443635, 2],
    [-0.881374, -1],
    [14.508658, 1000000],
    // ['#NUM!', 2], Don't know if NAN is possible
];
