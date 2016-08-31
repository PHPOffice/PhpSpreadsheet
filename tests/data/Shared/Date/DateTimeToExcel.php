<?php

// DateTime object                          Result           Comments
return [
    [new DateTime('1900-01-01'), 1.0], //  Excel 1900 base calendar date
    [new DateTime('1900-02-28'), 59.0], //  This and next test show gap for the mythical
    [new DateTime('1900-03-01'), 61.0], //      MS Excel 1900 Leap Year
    [new DateTime('1901-12-14'), 714.0], //  Unix Timestamp 32-bit Earliest Date
    [new DateTime('1903-12-31'), 1461.0],
    [new DateTime('1904-01-01'), 1462.0], //  Excel 1904 Calendar Base Date
    [new DateTime('1904-01-02'), 1463.0],
    [new DateTime('1960-12-19'), 22269.0],
    [new DateTime('1970-01-01'), 25569.0], //  Unix Timestamp Base Date
    [new DateTime('1982-12-07'), 30292.0],
    [new DateTime('2008-06-12'), 39611.0],
    [new DateTime('2038-01-19'), 50424.0], //  Unix Timestamp 32-bit Latest Date
    [new DateTime('1903-05-18 13:37:46'), 1234.56789],
    [new DateTime('1933-10-18 16:17:37'), 12345.6789],
    [new DateTime('2099-12-31'), 73050.0],
];
