<?php

declare(strict_types=1);

return [
    [2, '2.5, 1'],
    [-4, '"-2.5", "-2"'],
    [-4, '-2.5, 2'],
    [2, '2.5, -2'],
    [0.0, '0.0, 1'],
    'corrected with PR 4466' => [0.0, '123.456, 0'],
    [1.5, '1.5, 0.1'],
    [0.23, '0.234, 0.01'],
    [123, '123.456'],
    ['#VALUE!', '"ABC"'],
    [15, '17, 3'],
    [16, '19, 4'],
    [20, '24.3, 5'],
    [6, '6.7, 1'],
    [-10, '-8.1, 2'],
    [-4, '-5.5, 2, -1'],
    [-4, '-5.5, 2, 1'],
    [-6, '-5.5, 2, 0'],
    ['exception', ''],
    [0, ','],
    [0, ', 2'],
    [0, 'false'],
    [1, 'true'],
    ['#VALUE!', '"", 2'],
    [1, 'A2'],
    [2, 'A3'],
    [-4, 'A4'],
    [-6, 'A5'],
];
