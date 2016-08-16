<?php

//  Date String, Result

return [
    [
        '25-Dec-1899',
        '#VALUE!',
    ],
    [
        '31-Dec-1899',
        '#VALUE!',
    ],
    [
        '1-Jan-1900',
        1,
    ],
    [
        '1900/2/28',
        59,
    ],
    [
        '29-02-1900',
        '#VALUE!',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '29th February 1900',
        '#VALUE!',
    ],
    [
        '1900/3/1',
        61,
    ],
    [
        '13-12-1901',
        713,
    ],
    [
        '14-12-1901',
        714,
    ],
    [
        '1903/12/31',
        1461,
    ],
    [
        '1-Jan-1904',
        1462,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '2nd-Jan-1904',
        1463,
    ],
    [
        '19-12-1960',
        22269,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '1st January 1970',
        25569,
    ],
    [
        '7-Dec-1982',
        30292,
    ],
    [
        '1-1-2008',
        39448,
    ],
    [
        '2038-01-19',
        50424,
    ],
    [
        '2-6-2008',
        39601,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        'December 25th 2008',
        39807,
    ],
    [
        '1 Jan-2008',
        39448,
    ],
    // MS Excel success or failure dependent on country settings
    [
        '12-31-2008',
        39813,
    ],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [
        '31-12-2008',
        39813,
    ],
    // MS Excel success or failure dependent on country settings
    [
        '8/22/2008',
        39682,
    ],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [
        '22/8/2008',
        39682,
    ],
    [
        '22/8/08',
        39682,
    ],
    [
        '22-AUG-2008',
        39682,
    ],
    [
        '2008/02/23',
        39501,
    ],
    [
        '6-7-2008',
        39635,
    ],
    // MS Excel success or failure dependent on country settings
    [
        '28-2-2007',
        39141,
    ],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [
        '2-28-2007',
        39141,
    ],
    // Should fail because it's an invalid date, but PhpSpreadsheet currently adjusts to 1-3-2007 - FIX NEEDED
    [
        '29-2-2007',
        '#VALUE!',
    ],
    [
        '1/1/1999',
        36161,
    ],
    [
        '1954-07-20',
        19925,
    ],
    [
        '22 August 98',
        36029,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '1st March 2007',
        39142,
    ],
    [
        'The 1st day of March 2007',
        '#VALUE!',
    ],
    // 01/01 of the current year
    [
        '1 Jan',
        42370,
    ],
    // 31/12 of the current year
    [
        '31/12',
        42735,
    ],
    // Excel reads as 1st December 1931, not 31st December in current year
    [
        '12/31',
        11658,
    ],
    // 05/07 of the current year
    [
        '5-JUL',
        42556,
    ],
    // 05/07 of the current year
    [
        '5 Jul',
        42556,
    ],
    [
        '12/2008',
        39783,
    ],
    [
        '10/32',
        11963,
    ],
    [
        11,
        '#VALUE!',
    ],
    [
        true,
        '#VALUE!',
    ],
    [
        false,
        '#VALUE!',
    ],
    [
        1,
        '#VALUE!',
    ],
    [
        12345,
        '#VALUE!',
    ],
    [
        12,
        '#VALUE!',
    ],
    [
        '12-Feb-2010',
        40221,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        'Feb-12-2010',
        40221,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        'February-12-2010',
        40221,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        'February 12 2010',
        40221,
    ],
    [
        '18 Feb 2010',
        40227,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '17th 3rd 2010',
        40254,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        'Feb 18th 2010',
        40227,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '1st Feb 2010',
        40210,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '1st-Feb-2010',
        40210,
    ],
    [
        '1me Fev 2010',
        '#VALUE!',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        'February 1st 2010',
        40210,
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '2nd Feb 2010',
        40211,
    ],
    [
        'Second Feb 2010',
        '#VALUE!',
    ],
    [
        'First August 2010',
        '#VALUE!',
    ],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [
        '1st August 2010',
        40391,
    ],
    [
        '15:30:25',
        0,
    ],
];
