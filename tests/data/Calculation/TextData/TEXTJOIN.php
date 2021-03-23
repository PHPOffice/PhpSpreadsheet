<?php

return [
    [
        'ABCDE,FGHIJ',
        [',', true, 'ABCDE', 'FGHIJ'],
    ],
    [
        'ABCDEFGHIJ',
        ['', true, 'ABCDE', 'FGHIJ'],
    ],
    [
        '1-2-3',
        ['-', true, 1, 2, 3],
    ],
    [
        '<<::>>',
        ['::', true, '<<', '>>'],
    ],
    [
        'Καλό απόγευμα',
        [' ', true, 'Καλό', 'απόγευμα'],
    ],
    [
        'Boolean-TRUE',
        ['-', true, 'Boolean', '', true],
    ],
    [
        'Boolean-TRUE',
        ['-', true, 'Boolean', '  ', true],
    ],
    [
        'Boolean--TRUE',
        ['-', false, 'Boolean', '', true],
    ],
    [
        'C:\\Users\\Mark\\Documents\\notes.doc',
        ['\\', true, 'C:', 'Users', 'Mark', 'Documents', 'notes.doc'],
    ],
];
