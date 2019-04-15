<?php
return [

    [ // Office reference example #1
        'orange',
        4.19,
        [
            [4.14],
            [4.19],
            [5.17],
            [5.77],
            [6,39],
        ],
        [
            ['red'],
            ['orange'],
            ['yellow'],
            ['green'],
            ['blue'],
        ],
    ],
    [ // Office reference example #2
        'yellow',
        5.75,
        [
            [4.14],
            [4.19],
            [5.17],
            [5.77],
            [6,39],
        ],
        [
            ['red'],
            ['orange'],
            ['yellow'],
            ['green'],
            ['blue'],
        ],
    ],
    [ // Office reference example #3
        'blue',
        7.66,
        [
            [4.14],
            [4.19],
            [5.17],
            [5.77],
            [6,39],
        ],
        [
            ['red'],
            ['orange'],
            ['yellow'],
            ['green'],
            ['blue'],
        ],
    ],
    [ // Office reference example #4
        '#N/A',
        0,
        [
            [4.14],
            [4.19],
            [5.17],
            [5.77],
            [6,39],
        ],
        [
            ['red'],
            ['orange'],
            ['yellow'],
            ['green'],
            ['blue'],
        ],
    ],

    [ // Array form test
        'orange',
        4.2,
        [
            [4.14, 'red'],
            [4.19, 'orange'],
            [5.17, 'yellow'],
            [5.77, 'green'],
            [6,39, 'blue'],
        ]
    ],

    [
        5,
        'x',
        [
            [0, 0, 0, 'x', 'x'],
            [1, 2, 3, 4, 5]
        ]
    ],

    [
        'author_100',
        100,
        [
            [100],
            [101]
        ],
        [
            ['author_100'],
            ['author_101']
        ]
    ],

    [
        '#N/A',
        '10y2',
        [
            ['5y-1'],
            ['10y1'],
            ['10y2'],
        ],
        [
            [2.0],
            [7.0],
            [10.0],
        ],
    ]

];
