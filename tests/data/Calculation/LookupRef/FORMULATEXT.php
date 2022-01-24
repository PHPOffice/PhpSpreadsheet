<?php

return [
    [
        '#N/A',
        2,
    ],
    [
        '="ABC"',
        '="ABC"',
    ],
    [
        '=A1',
        '=A1',
    ],
    [
        '=\'Worksheet1\'!A1',
        '=\'Worksheet1\'!A1',
    ],
    [
        '=\'Works heet1\'!A1',
        '=\'Works heet1\'!A1',
    ],
    [
        '="HELLO WORLD"',
        '="HELLO WORLD"',
    ],
    [
        '=\'Work!sheet1\'!A5',
        '=\'Work!sheet1\'!A5',
    ],
    [
        '#N/A',
        'A1',
    ],
    [
        '#N/A',
        null,
    ],
    [
        '=SUM(B1,B2,B3)',
        '=SUM(B1,B2,B3)',
    ],
];
