<?php

return [
    ['#VALUE!'], // exception not enough args
    ['#VALUE!', '"ABC"'],
    [0, 0],
    [1.5, 2.25],
    [1.772454, M_PI],
    ['#NUM!', -2.1],
];
