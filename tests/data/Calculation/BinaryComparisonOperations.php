<?php

declare(strict_types=1);

return [
    [null, null, '=', true, true],
    [null, null, '<>', false, false],
    // Values of different types are ordered by type: numbers < text < FALSE < TRUE.
    // See the ascending order MATCH documents: ...-2, -1, 0, 1, 2, ..., A-Z, FALSE, TRUE.
    // LibreOffice treats logical values as numbers.
    ['', 75, '<', false, false],
    ['', 0, '>', true, true],
    [0, '', '<', true, true],
    ['a', 1E+100, '>', true, true],
    [1, 'a', '=', false, false],
    [1, 'a', '<>', true, true],
    [-1, 'a', '>=', false, false],
    ['a', -1, '<=', false, false],
    [false, 'zzz', '>', true, false],
    ['zzz', true, '<', true, false],
    [true, 1, '=', false, true],
    [true, 1, '>', true, false],
    [false, 0, '>', true, false],
    [false, 1, '<', false, true],
];
