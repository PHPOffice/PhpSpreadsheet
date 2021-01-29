<?php

return [
    ['#VALUE!'], // exception - not enough args
    ['#VALUE!', '"ABC"'],
    [35.51, '"35.51"'],
    [35.51, 35.51],
    [35.51, -35.51],
    [6, '"6"'],
    [7, '"-7"'],
    [0, 0],
];
