<?php

return [
    [
        '#N/A',
        'A1',
        '2',
    ],
    [
        '="ABC"',
        'A2',
        '="ABC"',
    ],
    [
        '=A1',
        'A3',
        '=A1',
    ],
    [
        '=\'Worksheet1\'!A1',
        'A4',
        '=\'Worksheet1\'!A1',
    ],
    [
        '="HELLO WORLD"',
        '\'Worksheet1\'!A5',
        '="HELLO WORLD"',
    ],
    [
        '#N/A',
        '\'Worksheet1\'!A6',
        '123',
    ],
];
