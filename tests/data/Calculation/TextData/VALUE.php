<?php

declare(strict_types=1);

return [
    [
        '1000',
        '1000',
    ],
    [
        '1000',
        '1 000',
    ],
    [
        '1000',
        '$1 000',
    ],
    [
        '#VALUE!',
        '£1 000',
    ],
    [
        '1.1',
        '1.1',
    ],
    [
        '1000.1',
        '1 000.1',
    ],
    [
        '#VALUE!',
        '13 Monkeys',
    ],
    [
        '41640',
        '1-Jan-2014',
    ],
    [
        '0.524259259259259',
        '12:34:56',
    ],
    [
        '0.11527777777778',
        '2:46 AM',
    ],
    'no arguments' => ['exception'],
    'bool argument' => ['#VALUE!', false],
    'null argument' => ['0', null],
    'issue 3574 null string invalid' => ['#VALUE!', ''],
    'issue 3574 blank string invalid' => ['#VALUE!', '  '],
    'issue 3574 non-blank numeric string okay' => [2, ' 2 '],
    'issue 3574 non-blank non-numeric string invalid' => ['#VALUE!', ' x '],
];
