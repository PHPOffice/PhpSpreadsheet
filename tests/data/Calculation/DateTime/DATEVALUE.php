<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

//  Date String, Result
// Note that Excel fails ordinal number forms but PhpSpreadsheet parses them
return [
    [ExcelError::VALUE(), '25-Dec-1899'],
    [ExcelError::VALUE(), '31-Dec-1899'],
    [1, '1-Jan-1900'],
    [59, '1900/2/28'],
    [60, '29-02-1900'],
    [60, '29th February 1900'], // ordinal
    [61, '1900/3/1'],
    [713, '13-12-1901'],
    [714, '14-12-1901'],
    [1461, '1903/12/31'],
    [1462, '1-Jan-1904'],
    [1463, '2nd-Jan-1904'], // ordinal
    [22269, '19-12-1960'],
    [25569, '1st January 1970'], // ordinal
    [30292, '7-Dec-1982'],
    [39448, '1-1-2008'],
    [50424, '2038-01-19'],
    [39601, '2-6-2008'],
    [39807, 'December 25th 2008'], // ordinal
    [39448, '1 Jan-2008'],
    // MS Excel success or failure dependent on country settings
    [39813, '12-31-2008'],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [39813, '31-12-2008'],
    // MS Excel success or failure dependent on country settings
    [39682, '8/22/2008'],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [39682, '22/8/2008'],
    [39682, '22/8/08'],
    [39682, '22-AUG-2008'],
    [39501, '2008/02/23'],
    [39635, '6-7-2008'],
    // MS Excel success or failure dependent on country settings
    [39141, '28-2-2007'],
    // PhpSpreadsheet tries to handle both US and UK formats, irrespective of country settings
    [39141, '2-28-2007'],
    [ExcelError::VALUE(), '29-2-2007'],
    [36161, '1/1/1999'],
    [19925, '1954-07-20'],
    [36029, '22 August 98'],
    [39142, '1st March 2007'], // ordinal
    [ExcelError::VALUE(), 'The 1st day of March 2007'],
    ['Y-01-01', '1 Jan'], // Jan 1 of the current year
    ['Y-12-31', '31/12'], // Dec 31 of the current year
    // Excel reads as 1st December 1931, not 31st December in current year.
    // This result is locale-dependent in Excel, in a manner not
    // supported by PhpSpreadsheet.
    [11658, '12/31'],
    ['Y-07-05', '5-JUL'], // July 5 of the current year
    ['Y-07-05', '5 July'], // July 5 of the current year
    [39783, '12/2008'],
    [11963, '10/32'],
    [ExcelError::VALUE(), 11],
    [ExcelError::VALUE(), 1],
    [ExcelError::VALUE(), 12345],
    [ExcelError::VALUE(), 12],
    [40210, 'Feb-2010'], // implicit day of month is 1
    [40221, '12-Feb-2010'],
    [40221, 'Feb-12-2010'], // MS Excel #VALUE!
    [40221, 'February-12-2010'], // MS Excel #VALUE!
    [40221, 'February 12 2010'], // MS Excel #VALUE!
    [40227, '18 Feb 2010'],
    [40254, '17th 3rd 2010'], // MS Excel #VALUE!
    [40227, 'Feb 18th 2010'], // MS Excel #VALUE!
    [40210, '1st Feb 2010'], // MS Excel #VALUE!
    [40210, '1st-Feb-2010'], // Excel #VALUE!
    [ExcelError::VALUE(), '1me Fev 2010'],
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [40210, 'February 1st 2010'], // Excel #VALUE!
    [40211, '2nd Feb 2010'], // Excel #VALUE!
    [ExcelError::VALUE(), 'Second Feb 2010'],
    [ExcelError::VALUE(), 'First August 2010'],
    [40391, '1st August 2010'], // Excel #VALUE!
    [0, '15:30:25'],
    [ExcelError::VALUE(), 'ABCDEFGHIJKMNOPQRSTUVWXYZ'],
    [ExcelError::VALUE(), 1999],
    [ExcelError::VALUE(), '32/32'],
    [ExcelError::VALUE(), '1910-'],
    [ExcelError::VALUE(), '10--'],
    [ExcelError::VALUE(), '--10'],
    [ExcelError::VALUE(), '--1910'],
    //[#VALUE!, -JUL-1910], We can parse this, Excel cant
    [ExcelError::VALUE(), '2008-08-'],
    [36751, '0-08-13'],
    [ExcelError::VALUE(), false],
    [ExcelError::VALUE(), true],
];
