<?php

return [
    [
        27,
        10, 7, 9, 27, 2,
    ],
    [
        10,
        10, 7, 9, '27', 2,
    ],
    [
        0,
        null, 'STRING', true, '', '27',
    ],
    'error among arguments' => [
        '#DIV/0!',
        1, 3, '=5/0', -2,
    ],
];
