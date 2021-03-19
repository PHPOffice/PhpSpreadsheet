<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"'],
    [1.316958, 2],
    [0, 1],
    ['#NUM!', 0],
    ['#NUM!', -1],
];
