<?php

declare(strict_types=1);

return [
    ['exception', ''],
    ['#VALUE!', '"ABC"'],
    [0, '0'],
    [1.472219, '"0.9"'],
    [-1.472219, '-0.9'],
    ['#NUM!', '1'],
    ['#NUM!', '-1'],
    [0, 'Q15'],
    [0, 'false'],
    ['#NUM!', 'true'],
    [1.098612289, 'A2'],
];
