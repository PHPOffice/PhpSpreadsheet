<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"', 1],
    ['#VALUE!', 35.51, '"test"'],
    ['#VALUE!', 35.51], // exception - not enough args
    [35.5, '"35.51"', '"1"'],
    [35.5, 35.51, 1],
    [40, 35.51, -1],
];
