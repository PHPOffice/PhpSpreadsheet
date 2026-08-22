<?php

declare(strict_types=1);

return [
    [
        '#VALUE!',
        'ABC',
    ],
    [
        '#VALUE!',
        -5,
    ],
    [
        '#VALUE!',
        0,
    ],
    [
        'A',
        65,
    ],
    [
        '{',
        123,
    ],
    [
        '~',
        126,
    ],
    [
        ['Á', 'Á', '¡'],
        193,
    ],
    [
        ['ÿ', 'ÿ', 'ˇ'],
        255,
    ],
    [
        ['#VALUE!', 'Ā'],
        256,
    ],
    [
        ['#VALUE!', '⽇'],
        12103,
    ],
    [
        ['#VALUE!', 'œ'],
        0x153,
    ],
    [
        ['#VALUE!', 'ƒ'],
        0x192,
    ],
    [
        ['#VALUE!', '℅'],
        0x2105,
    ],
    [
        ['#VALUE!', '∑'],
        0x2211,
    ],
    [
        ['#VALUE!', '†'],
        0x2020,
    ],
    'example 1 different location all 3' => [
        ['†', mb_chr(134, 'UTF-8'), 'Ü'],
        134,
    ],
    'example 2 different location all 3' => [
        ['€', mb_chr(128, 'UTF-8'), 'Ä'],
        128,
    ],
    'non-ascii same win-1252 vs unicode, different mac' => [
        ['Û', 'Û', '€'],
        219,
    ],
    'after currency symbol placeholder' => [
        ['Ü', 'Ü', '‹'],
        220,
    ],
    'Example 3 where MAC differs from others' => [
        ['Ð', 'Ð', '–'],
        0xD0,
    ],
    'last assigned Unicode character' => [
        ['#VALUE!', mb_chr(0x10FFFD, 'UTF-8')],
        0x10FFFD,
    ],
    'highest possible code point' => [
        ['#VALUE!', '#N/A'],
        0x10FFFF,
    ],
    'above highest possible code point' => [
        '#VALUE!',
        0x110000,
    ],
    'nbsp for win/uni, dagger for Mac' => [
        ["\u{A0}", "\u{A0}", '†'],
        160,
    ],
    'omitted argument' => ['exception'],
    'non-printable' => ["\x02", 2],
    'bool argument' => ["\x01", true],
    'null argument' => ['#VALUE!', null],
    'ascii 1 is 49' => ['1', 49],
    'ascii 0 is 48' => ['0', 48],
];
