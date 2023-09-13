<?php

declare(strict_types=1);

return [
    [
        false,
        'A1',
    ],
    [
        false,
        '$A$1',
    ],
    [
        true,
        'A1,C3',
    ],
    [
        true,
        'A1:A10',
    ],
    [
        true,
        'A1:A10,C4',
    ],
];
