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
    // Chinese date format
    [
        '1960年12月19日',
        22269.0625,
        '[DBNum1][$-804]yyyy"年"m"月"d"日";@',
    ],
    [
        '1960年12月',
        22269.0625,
        '[DBNum1][$-804]yyyy"年"m"月";@',
    ],
    [
        '12月19日',
        22269.0625,
        '[DBNum1][$-804]m"月"d"日";@',
    ],
];
