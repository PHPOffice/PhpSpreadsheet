<?php

return [
    [
        22269.0625,
        'dd-mm-yyyy hh:mm:ss',
        '19-12-1960 01:30:00',
    ],
    // Oasis uses upper-case
    [
        22269.0625,
        'MM/DD/YYYY HH:MM:SS',
        '12/19/1960 01:30:00',
    ],
    // Date with plaintext escaped with a \
    [
        22269.0625,
        'yyyy-mm-dd\\Thh:mm:ss',
        '1960-12-19T01:30:00',
    ],
    // Date with plaintext in quotes
    [
        22269.0625,
        'yyyy-mm-dd"T"hh:mm:ss \\Z',
        '1960-12-19T01:30:00 Z',
    ],
    // Date with quoted formatting characters
    [
        22269.0625,
        '"y-m-d" yyyy-mm-dd "h:m:s" hh:mm:ss',
        'y-m-d 1960-12-19 h:m:s 01:30:00',
    ],
    // Date with quoted formatting characters
    [
        22269.0625,
        '"y-m-d "yyyy-mm-dd" h:m:s "hh:mm:ss',
        'y-m-d 1960-12-19 h:m:s 01:30:00',
    ],
    // Chinese date format
    [
        22269.0625,
        '[DBNum1][$-804]yyyy"年"m"月"d"日";@',
        '1960年12月19日',
    ],
    [
        22269.0625,
        '[DBNum1][$-804]yyyy"年"m"月";@',
        '1960年12月',
    ],
    [
        22269.0625,
        '[DBNum1][$-804]m"月"d"日";@',
        '12月19日',
    ],
];
