<?php

//  Date String, Result

return [
    [
        '#VALUE!',
        '25-Dec-1899',
    ],
    [
        '#VALUE!',
        '31-Dec-1899',
    ],
    [
        1,
        '1-Jan-1900',
    ],
    [
        59,
        '1900/2/28',
    ],
    [
        '#VALUE!',
        '29-02-1900',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '#VALUE!',
        '29th February 1900',
    ],
    [
        61,
        '1900/3/1',
    ],
    [
        713,
        '13-12-1901',
    ],
    [
        714,
        '14-12-1901',
    ],
    [
        1461,
        '1903/12/31',
    ],
    [
        1462,
        '1-Jan-1904',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        1463,
        '2nd-Jan-1904',
    ],
    [
        22269,
        '19-12-1960',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        25569,
        '1st January 1970',
    ],
    [
        30292,
        '7-Dec-1982',
    ],
    [
        39448,
        '1-1-2008',
    ],
    [
        50424,
        '2038-01-19',
    ],
    [
        39601,
        '2-6-2008',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        39807,
        'December 25th 2008',
    ],
    [
        39448,
        '1 Jan-2008',
    ],
    // MS Excel success or failure dependent on country settings
    [
        39813,
        '12-31-2008',
    ],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [
        39813,
        '31-12-2008',
    ],
    // MS Excel success or failure dependent on country settings
    [
        39682,
        '8/22/2008',
    ],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [
        39682,
        '22/8/2008',
    ],
    [
        39682,
        '22/8/08',
    ],
    [
        39682,
        '22-AUG-2008',
    ],
    [
        39501,
        '2008/02/23',
    ],
    [
        39635,
        '6-7-2008',
    ],
    // MS Excel success or failure dependent on country settings
    [
        39141,
        '28-2-2007',
    ],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [
        39141,
        '2-28-2007',
    ],
    // Should fail because it's an invalid date, but PhpSpreadsheet currently adjusts to 1-3-2007 - FIX NEEDED
    [
        '#VALUE!',
        '29-2-2007',
    ],
    [
        36161,
        '1/1/1999',
    ],
    [
        19925,
        '1954-07-20',
    ],
    [
        36029,
        '22 August 98',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        39142,
        '1st March 2007',
    ],
    [
        '#VALUE!',
        'The 1st day of March 2007',
    ],
    // 01/01 of the current year
    [
        43101,
        '1 Jan',
    ],
    // 31/12 of the current year
    [
        43465,
        '31/12',
    ],
    // Excel reads as 1st December 1931, not 31st December in current year
    [
        11658,
        '12/31',
    ],
    // 05/07 of the current year
    [
        43286,
        '5-JUL',
    ],
    // 05/07 of the current year
    [
        43286,
        '5 Jul',
    ],
    [
        39783,
        '12/2008',
    ],
    [
        11963,
        '10/32',
    ],
    [
        '#VALUE!',
        11,
    ],
    [
        '#VALUE!',
        true,
    ],
    [
        '#VALUE!',
        false,
    ],
    [
        '#VALUE!',
        1,
    ],
    [
        '#VALUE!',
        12345,
    ],
    [
        '#VALUE!',
        12,
    ],
    [
        40221,
        '12-Feb-2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40221,
        'Feb-12-2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40221,
        'February-12-2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40221,
        'February 12 2010',
    ],
    [
        40227,
        '18 Feb 2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40254,
        '17th 3rd 2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40227,
        'Feb 18th 2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40210,
        '1st Feb 2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40210,
        '1st-Feb-2010',
    ],
    [
        '#VALUE!',
        '1me Fev 2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40210,
        'February 1st 2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40211,
        '2nd Feb 2010',
    ],
    [
        '#VALUE!',
        'Second Feb 2010',
    ],
    [
        '#VALUE!',
        'First August 2010',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        40391,
        '1st August 2010',
    ],
    [
        0,
        '15:30:25',
    ],
    [
        '#VALUE!',
        'ABCDEFGHIJKMNOPQRSTUVWXYZ',
    ],
];
