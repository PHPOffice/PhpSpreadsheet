<?php

// DateTime object                          Result           Comments
return [
    // Excel 1900 base calendar date
    [
        1.0,
        new DateTime('1900-01-01'),
    ],
    // This and next test show gap for the mythical
    [
        59.0,
        new DateTime('1900-02-28'),
    ],
    // MS Excel 1900 Leap Year
    [
        61.0,
        new DateTime('1900-03-01'),
    ],
    // Unix Timestamp 32-bit Earliest Date
    [
        714.0,
        new DateTime('1901-12-14'),
    ],
    [
        1461.0,
        new DateTime('1903-12-31'),
    ],
    // Excel 1904 Calendar Base Date
    [
        1462.0,
        new DateTime('1904-01-01'),
    ],
    [
        1463.0,
        new DateTime('1904-01-02'),
    ],
    [
        22269.0,
        new DateTime('1960-12-19'),
    ],
    // Unix Timestamp Base Date
    [
        25569.0,
        new DateTime('1970-01-01'),
    ],
    [
        30292.0,
        new DateTime('1982-12-07'),
    ],
    [
        39611.0,
        new DateTime('2008-06-12'),
    ],
    // Unix Timestamp 32-bit Latest Date
    [
        50424.0,
        new DateTime('2038-01-19'),
    ],
    [
        1234.56789,
        new DateTime('1903-05-18 13:37:46'),
    ],
    [
        12345.6789,
        new DateTime('1933-10-18 16:17:37'),
    ],
    [
        73050.0,
        new DateTime('2099-12-31'),
    ],
];
