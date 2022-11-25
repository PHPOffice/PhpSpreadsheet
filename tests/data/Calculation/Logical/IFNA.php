<?php

return [
    [
        55,
        55, 'not found',
    ],
    [
        'not found',
        '#N/A', 'not found',
    ],
    'non-NA error' => ['#VALUE!', '#VALUE!', 'not found'],
    'empty cell treated as 0' => [0, null, 'Error'],
];
