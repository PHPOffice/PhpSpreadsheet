<?php

return [
    [
        '19-12-1960 01:30:00',
        22269.0625,
        'dd-mm-yyyy hh:mm:ss',
    ],
    // Oasis uses upper-case
    [
        '12/19/1960 01:30:00',
        22269.0625,
        'MM/DD/YYYY HH:MM:SS',
    ],
    // Date with plaintext escaped with a \
    [
        '1960-12-19T01:30:00',
        22269.0625,
        'yyyy-mm-dd\\Thh:mm:ss',
    ],
    // Date with plaintext in quotes
    [
        '1960-12-19T01:30:00 Z',
        22269.0625,
        'yyyy-mm-dd"T"hh:mm:ss \\Z',
    ],
    // Date with quoted formatting characters
    [
        'y-m-d 1960-12-19 h:m:s 01:30:00',
        22269.0625,
        '"y-m-d" yyyy-mm-dd "h:m:s" hh:mm:ss',
    ],
    // Date with quoted formatting characters
    [
        'y-m-d 1960-12-19 h:m:s 01:30:00',
        22269.0625,
        '"y-m-d "yyyy-mm-dd" h:m:s "hh:mm:ss',
    ],
    [
        '07:35:00 AM',
        43270.315972222,
        'hh:mm:ss\ AM/PM',
    ],
    [
        '02:29:00 PM',
        43270.603472222,
        'hh:mm:ss\ AM/PM',
    ],
    [
        '8/20/2018',
        43332,
        '[$-409]m/d/yyyy',
    ],
    [
        '8/20/2018',
        43332,
        '[$-1010409]m/d/yyyy',
    ],
    [
        '27:15',
        1.1354166666667,
        '[h]:mm',
    ],
    [
        '19331018',
        12345.6789,
        '[DBNum4][$-804]yyyymmdd;@',
    ],
    // Technically should be １９３３１０１８
    [
        '19331018',
        12345.6789,
        '[DBNum3][$-zh-CN]yyyymmdd;@',
    ],
];
