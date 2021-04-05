<?php

return [
    [50, 5, 15, 30],
    [52, 5, 15, 30, 2],
    [53.1, 5.7, 15, 30, 2.4],
    ['#VALUE!', 5.7, 'X', 30, 2.4], // error here conflicts with SUMIF
];
