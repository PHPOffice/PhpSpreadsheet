<?php

return [
    [
        'QWDFGYUIOP',
        'QWERTYUIOP',
        'ERT',
        'DFG',
    ],
    [
        'Mxrk Bxker',
        'Mark Baker',
        'a',
        'x',
    ],
    [
        'Mxrk Baker',
        'Mark Baker',
        'a',
        'x',
        1,
    ],
    [
        'Mark Baker',
        'Mark Baker',
        'x',
        'a',
        1,
    ],
    'Unicode equivalence is not supported' => [
        "\u{0061}\u{030A}",
        "\u{0061}\u{030A}",
        "\u{00E5}",
        'x',
    ],
    'Multibytes are supported' => [
        'x',
        "\u{00E5}",
        "\u{00E5}",
        'x',
    ],
];
