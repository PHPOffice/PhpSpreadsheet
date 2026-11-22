<?php

declare(strict_types=1);

return [
    [
        3,
        'E',
        'QWERTYUIOP',
    ],
    [
        4,
        'D',
        'ABCDEFGHI',
    ],
    [
        4,
        'E',
        true,
    ],
    [
        5,
        'E',
        false,
    ],
    [
        2,
        'A',
        'Mark Baker',
    ],
    [
        '#VALUE!',
        'C',
        'Mark Baker',
    ],
    [
        7,
        'A',
        'Mark Baker',
        3,
    ],
    [
        4,
        'K',
        'Mark Baker',
    ],
    [
        8,
        'K',
        'Mark Baker',
        5,
    ],
    [
        2,
        'A',
        'Mark Baker',
        2,
    ],
    [
        1,
        '',
        'Mark Baker',
    ],
    [
        1,
        'Ενα',
        'Ενα δύο τρία τέσσερα πέντε',
    ],
    [
        9,
        'τρία',
        'Ενα δύο τρία τέσσερα πέντε',
    ],
    [
        22,
        'πέντε',
        'Ενα δύο τρία τέσσερα πέντε',
    ],
    [
        1,
        'Ενα',
        'ΕΝΑ ΔΥΟ ΤΡΙΑ ΤΕΣΣΕΡΑ ΠΕΝΤΕ',
    ],
    [
        9,
        'τρία',
        'ΕΝΑ ΔΎΟ ΤΡΊΑ ΤΈΣΣΕΡΑ ΠΈΝΤΕ',
    ],
    [
        22,
        'πέντε',
        'ΕΝΑ ΔΎΟ ΤΡΊΑ ΤΈΣΣΕΡΑ ΠΈΝΤΕ',
    ],
    [
        '#VALUE!',
        'BITE',
        'BIT',
    ],
    'wildcard question mark' => [
        2,
        '?c',
        'abc',
    ],
    'wildcard question mark is case-insensitive' => [
        2,
        'b?d',
        'ABCD',
    ],
    'wildcard asterisk' => [
        2,
        'b*d',
        'abcd',
    ],
    'wildcard asterisk alone' => [
        1,
        '*',
        'abc',
    ],
    'wildcard with offset' => [
        4,
        'b?',
        'abcbd',
        3,
    ],
    'wildcard with multibyte characters' => [
        6,
        'ύ?',
        'ΕΝΑ ΔΎΟ',
        2,
    ],
    'wildcard not found' => [
        '#VALUE!',
        'x*',
        'abc',
    ],
    'tilde escapes asterisk' => [
        2,
        '~*',
        'a*b',
    ],
    'tilde escapes question mark' => [
        3,
        '~?',
        'ab?c',
    ],
    'regex characters are literal' => [
        '#VALUE!',
        'a.c',
        'abc',
    ],
    'Boolean Needle' => [
        '#VALUE!',
        true,
        'Mark Baker',
    ],
    'no arguments' => ['exception'],
    'one argument' => ['exception', 'string'],
];
