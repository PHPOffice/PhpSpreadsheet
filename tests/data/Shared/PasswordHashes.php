<?php

return [
    [
        'BCDE',
        'PhpSpreadsheet',
    ],
    [
        '877D',
        'Mark Baker',
    ],
    [
        'C0EA',
        '!+&=()~§±æþ',
    ],
    [
        'C07E',
        '秘密口令',
    ],
    [
        '99E8',
        'leyndarmál lykilorð',
    ],
    [
        'CE4B',
        '',
    ],
    [
        'O6EXRLpLEDNJDL/AzYtnnA4O4bY=',
        '',
        'SHA-1',
    ],
    [
        'GYvlIMljDI1Czc4jfWrGaxU5pxl9n5Og0KUzyAfYxwk=',
        'PhpSpreadsheet',
        'SHA-256',
        'Php_salt',
        1000,
    ],
    [
        'sSHdxQv9qgpkr4LDT0bYQxM9hOQJFRhJ4D752/NHQtDDR1EVy67NCEW9cPd6oWvCoBGd96MqKpuma1A7pN1nEA==',
        'Mark Baker',
        'SHA-512',
        'Mark_salt',
        10000,
    ],
    [
        'r9KVLLCKIYOILvE2rcby+g==',
        '!+&=()~§±æþ',
        'MD5',
        'Symbols_salt',
        100000,
    ],
    // Additional tests suggested by Issue #1897
    ['DCDF', 'ABCDEFGHIJKLMNOPQRSTUVW'],
    ['ECD1', 'ABCDEFGHIJKLMNOPQRSTUVWX'],
    ['88D2', 'ABCDEFGHIJKLMNOPQRSTUVWXY'],
    'password too long' => ['exception', str_repeat('x', 256)],
];
