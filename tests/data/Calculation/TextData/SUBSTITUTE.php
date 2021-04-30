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
        'Mark Bxker',
        'Mark Baker',
        'a',
        'x',
        2,
    ],
    [
        'Mark Bakker',
        'Mark Baker',
        'k',
        'kk',
        2,
    ],
    [
        'Mark Baker',
        'Mark Baker',
        'x',
        'a',
        1,
    ],
    [
        'Ενα δύο αρία αέσσερα πέναε',
        'Ενα δύο τρία τέσσερα πέντε',
        'τ',
        'α',
    ],
    [
        'Ενα δύο τρία αέσσερα πέντε',
        'Ενα δύο τρία τέσσερα πέντε',
        'τ',
        'α',
        2,
    ],
    [
        'Ενα δύο τρία ατέσσερα πέντε',
        'Ενα δύο τρία τέσσερα πέντε',
        'τ',
        'ατ',
        2,
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
