<?php

declare(strict_types=1);

return [
    [
        '$123.46',
        123.456,
        '$#,##0.00',
    ],
    [
        '$-123.46',
        -123.456,
        '$#,##0.00',
    ],
    [
        '123.46',
        123.456,
        '#,##0.00',
    ],
    [
        '123',
        123.456,
        '#,##0',
    ],
    [
        '00123',
        123.456,
        '00000',
    ],
    [
        '$123,456.79',
        123456.789,
        '$#,##0.00',
    ],
    [
        '123,456.79',
        123456.789,
        '#,##0.00',
    ],
    [
        '1.23E+5',
        123456.789,
        '0.00E+00',
    ],
    [
        '-1.23E+5',
        -123456.789,
        '0.00E+00',
    ],
    [
        '1.23E-5',
        1.2345E-5,
        '0.00E+00',
    ],
    [
        '1960-12-19',
        '19-Dec-1960',
        'yyyy-mm-dd',
    ],
    [
        '2012-01-01',
        '1-Jan-2012',
        'yyyy-mm-dd',
    ],
    'time (issue 3409)' => [
        '09:01:00',
        '09:01',
        'HH:MM:SS',
    ],
    'datetime' => [
        '15-Feb-2014 04:17:00 PM',
        '2014-02-15 16:17',
        'dd-mmm-yyyy HH:MM:SS AM/PM',
    ],
    'datetime integer' => [
        '1900-01-06 00:00',
        6,
        'yyyy-mm-dd hh:mm',
    ],
    'datetime integer as string' => [
        '1900-01-06 00:00',
        '6',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime 2 integers without date delimiters' => [
        '5 6',
        '5 6',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime 2 integers separated by hyphen' => [
        (new DateTimeImmutable())->format('Y') . '-05-13 00:00',
        '5-13',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime string date only' => [
        '1951-01-23 00:00',
        'January 23, 1951',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime string time followed by date' => [
        '1952-05-02 03:54',
        '3:54 May 2, 1952',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime string date followed by time pm' => [
        '1952-05-02 15:54',
        'May 2, 1952 3:54 pm',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime string date followed by time p' => [
        '1952-05-02 15:54',
        'May 2, 1952 3:54 p',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime decimal string interpreted as time' => [
        '1900-01-02 12:00',
        '2.5',
        'yyyy-mm-dd hh:mm',
    ],
    'datetime unparseable string' => [
        'xyz',
        'xyz',
        'yyyy-mm-dd hh:mm',
    ],
    [
        '1 3/4',
        1.75,
        '# ?/?',
    ],
    'no arguments' => ['exception'],
    'one argument' => ['exception', 1.75],
    'boolean in lieu of string' => ['TRUE', true, '@'],
    'system long date format' => ['Sunday, January 1, 2012', '1-Jan-2012', '[$-x-sysdate]'],
];
