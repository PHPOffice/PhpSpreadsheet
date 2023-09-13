<?php

declare(strict_types=1);

return [
    [
        [
            'B4',
            'B5',
            'B6',
        ],
        'B4:B6',
    ],
    [
        [
            'B4',
            'D4',
            'B5',
            'D5',
            'B6',
            'D6',
        ],
        'B4:B6,D4:D6',
    ],
    [
        [
        ],
        'B4:B6 D4:D6',
    ],
    [
        [
            'B4',
            'C4',
            'D4',
            'B5',
            'C5',
            'D5',
            'B6',
            'C6',
            'D6',
        ],
        'B4:D6',
    ],
    [
        [
            'B4',
            'C4',
            'D4',
            'B5',
            'C5',
            'D5',
            'E5',
            'B6',
            'C6',
            'D6',
            'E6',
            'C7',
            'D7',
            'E7',
        ],
        'B4:D6,C5:E7',
    ],
    [
        [
            'C5',
            'D5',
            'C6',
            'D6',
        ],
        'B4:D6 C5:E7',
    ],
    [
        [
            'B2',
            'C2',
            'D2',
            'B3',
            'C3',
            'D3',
            'E3',
            'B4',
            'C4',
            'D4',
            'E4',
            'F4',
            'C5',
            'D5',
            'E5',
            'F5',
            'D6',
            'E6',
            'F6',
        ],
        'B2:D4,C5:D5,E3:E5,D6:E6,F4:F6',
    ],
    [
        [
            'B2',
            'C2',
            'D2',
            'B3',
            'C3',
            'D3',
            'E3',
            'B4',
            'C4',
            'D4',
            'E4',
            'F4',
            'C5',
            'D5',
            'E5',
            'F5',
            'D6',
            'E6',
            'F6',
        ],
        'B2:D4,C3:E5,D4:F6',
    ],
    [
        [
            'B5',
        ],
        'B4:B6 B5',
    ],
    [
        [
            'Z2',
            'AA2',
            'Z3',
            'AA3',
        ],
        'Z2:AA3',
    ],
    [
        [
            'Sheet1!D3',
        ],
        'Sheet1!D3',
    ],
    [
        [
            'Sheet1!D3',
            'Sheet1!E3',
            'Sheet1!D4',
            'Sheet1!E4',
        ],
        'Sheet1!D3:E4',
    ],
    [
        [
            "'Sheet 1'!D3",
            "'Sheet 1'!E3",
            "'Sheet 1'!D4",
            "'Sheet 1'!E4",
        ],
        "'Sheet 1'!D3:E4",
    ],
    [
        [
            "'Mark''s Sheet'!D3",
            "'Mark''s Sheet'!E3",
            "'Mark''s Sheet'!D4",
            "'Mark''s Sheet'!E4",
        ],
        "'Mark's Sheet'!D3:E4",
    ],
];
