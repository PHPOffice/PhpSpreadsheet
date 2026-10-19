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
    'unsigned numeric strings compared as strings' => ['32', '4', '<', true, true],
    'numeric string with leading - ignores minus sign case 1 (ods diff)' => ['-3', '25', '>', true, false],
    'numeric string with leading - ignores minus sign case 2' => ['-3', '40', '<', true, true],
    'equal numeric strings case 1' => ['1', '1', '=', true, true],
    'equal numeric strings case 2' => ['1', '1', '<>', false, false],
    'equal numeric strings case 3' => ['1', '1', '<=', true, true],
    'numeric strings #1>#2 case 1' => ['2', '1', '>', true, true],
    'numeric strings #1>#2 case 2' => ['2', '1', '<>', true, true],
    'numeric strings #1>#2 case 3' => ['2', '1', '<=', false, false],
    'numeric strings #1<#2 case 1' => ['2', '3', '<', true, true],
    'numeric strings #1<#2 case 2' => ['2', '3', '<>', true, true],
    'numeric strings #1<#2 case 3' => ['2', '3', '>=', false, false],
    'plus/plus strings' => ['+2', '+3', '>', false, false],
    'plus/minus strings (ods diff)' => ['+2', '-3', '>', false, true],
    'plus/unsigned strings' => ['+1', '0', '<', true, true],
    'unsigned/plus strings' => ['3', '+8', '>', true, true],
    'minus/plus strings (ods diff)' => ['-4', '+3', '>', true, false],
];
