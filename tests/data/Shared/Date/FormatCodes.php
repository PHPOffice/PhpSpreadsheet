<?php

declare(strict_types=1);

// Excel Format Code                     Result
return [
    [
        false,
        'General',
    ],
    [
        false,
        '@',
    ],
    [
        false,
        '0',
    ],
    [
        false,
        '0.00',
    ],
    [
        false,
        '#,##0.00',
    ],
    [
        false,
        '#,##0.00_-',
    ],
    [
        false,
        '0%',
    ],
    [
        false,
        '0.00%',
    ],
    [
        true,
        'yyyy-mm-dd',
    ],
    [
        true,
        'yy-mm-dd',
    ],
    [
        true,
        'dd/mm/yy',
    ],
    [
        true,
        'd/m/y',
    ],
    [
        true,
        'd-m-y',
    ],
    [
        true,
        'd-m',
    ],
    [
        true,
        'm-y',
    ],
    [
        true,
        'mm-dd-yy',
    ],
    [
        true,
        'd-mmm-yy',
    ],
    [
        true,
        'd-mmm',
    ],
    [
        true,
        'mmm-yy',
    ],
    [
        true,
        'm/d/yy h:mm',
    ],
    [
        true,
        'd/m/y h:mm',
    ],
    [
        true,
        'h:mm AM/PM',
    ],
    [
        true,
        'h:mm:ss AM/PM',
    ],
    [
        true,
        'h:mm',
    ],
    [
        true,
        'h:mm:ss',
    ],
    [
        true,
        'mm:ss',
    ],
    [
        true,
        'h:mm:ss',
    ],
    [
        true,
        'i:s.S',
    ],
    [
        true,
        'h:mm:ss;@',
    ],
    [
        true,
        'yy/mm/dd;@',
    ],
    [
        false,
        '"$" #,##0.00_-',
    ],
    [
        false,
        '$#,##0_-',
    ],
    [
        false,
        '[$EUR ]#,##0.00_-',
    ],
    [
        false,
        '_[$EUR ]#,##0.00_-',
    ],
    [
        false,
        '[Green]#,##0.00;[Red]#,##0.00_-',
    ],
    [
        false,
        '#,##0.00 "dollars"',
    ],
    [
        true,
        '"date " y-m-d',
    ],
    [
        false,
        '\C\H\-00000',
    ],
    [
        false,
        '\D-00000',
    ],
    [true, '[$-F800]'],
    [true, 'hello[$-F400]goodbye'],
    [false, '[$-F401]'],
    [true, '[$-x-sysdate]'],
    [true, '[$-x-systime]'],
    [false, '[$-x-systim]'],
];
