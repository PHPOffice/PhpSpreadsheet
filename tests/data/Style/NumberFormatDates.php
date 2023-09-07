<?php

declare(strict_types=1);

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
    // Date with fractional/decimal time
    [
        '2023/02/28 0:00:00.000',
        44985,
        'yyyy/mm/dd\ h:mm:ss.000',
    ],
    [
        '2023/02/28 07:35:02.400',
        44985.316,
        'yyyy/mm/dd\ hh:mm:ss.000',
    ],
    [
        '2023/02/28 07:35:13.067',
        44985.316123456,
        'yyyy/mm/dd\ hh:mm:ss.000',
    ],
    [
        '2023/02/28 07:35:13.07',
        44985.316123456,
        'yyyy/mm/dd\ hh:mm:ss.00',
    ],
    [
        '2023/02/28 07:35:13.1',
        44985.316123456,
        'yyyy/mm/dd\ hh:mm:ss.0',
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
    'hour with leading 0 and minute' => [
        '03:36',
        1.15,
        'hh:mm',
    ],
    'hour without leading 0 and minute' => [
        '3:36',
        1.15,
        'h:mm',
    ],
    'hour truncated not rounded' => [
        '27',
        1.15,
        '[hh]',
    ],
    'interval hour > 10 so no need for leading 0 and minute' => [
        '27:36',
        1.15,
        '[hh]:mm',
    ],
    'interval hour > 10 no leading 0 and minute' => [
        '27:36',
        1.15,
        '[h]:mm',
    ],
    'interval hour with leading 0 and minute' => [
        '03:36',
        0.15,
        '[hh]:mm',
    ],
    'interval hour no leading 0 and minute' => [
        '3:36',
        0.15,
        '[h]:mm',
    ],
    'interval hours > 100 and minutes no need for leading 0' => [
        '123:36',
        5.15,
        '[hh]:mm',
    ],
    'interval hours > 100 and minutes no leading 0' => [
        '123:36',
        5.15,
        '[h]:mm',
    ],
    'interval minutes > 10 no need for leading 0' => [
        '1656',
        1.15,
        '[mm]',
    ],
    'interval minutes > 10 no leading 0' => [
        '1656',
        1.15,
        '[m]',
    ],
    'interval minutes < 10 leading 0' => [
        '07',
        0.005,
        '[mm]',
    ],
    'interval minutes < 10 no leading 0' => [
        '7',
        0.005,
        '[m]',
    ],
    'interval minutes and seconds' => [
        '07:12',
        0.005,
        '[mm]:ss',
    ],
    'interval seconds' => [
        '432',
        0.005,
        '[ss]',
    ],
    'interval seconds rounded up leading 0' => [
        '09',
        0.0001,
        '[ss]',
    ],
    'interval seconds rounded up no leading 0' => [
        '9',
        0.0001,
        '[s]',
    ],
    'interval seconds rounded down' => [
        '6',
        0.00007,
        '[s]',
    ],
];
