<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [6890.0, 18, 11, 11], // year less than 1900, adds 1900 (1918-11-11)
    [44809.0, 122, 9, 5], // year less than 1900, adds 1900 (2022-09-05)
    [693845.0, 1899, 9, 5], // year less than 1900, adds 1900 (3799-09-05)
    [1.0, 1900, 1, 1], // Excel 1900 Calendar BaseDate
    [59.0, 1900, 2, 28], // Day before Excel mythical 1900 leap day
    [60.0, 1900, 2, 29], // Excel mythical 1900 leap day
    [61.0, 1900, 3, 1], // Day after Excel mythical 1900 leap day
    [713.0, 1901, 12, 13], // Day after actual 1904 leap day
    [714.0, 1901, 12, 14], // signed 32-bit Unix Timestamp Earliest Date
    [1461.0, 1903, 12, 31], // Day before Excel 1904 Calendar Base Date
    [1462.0, 1904, 1, 1], // Excel 1904 Calendar Base Date
    [1463.0, 1904, 1, 2], // Day after Excel 1904 Calendar Base Date
    [22269.0, 1960, 12, 19],
    [25569.0, 1970, 1, 1], // Unix Timestamp Base Date
    [30292.0, 1982, 12, 7],
    [39611.0, 2008, 6, 12],
    [50000.0, 2036, 11, 21],
    [50424.0, 2038, 1, 19], // 32-bit signed Unix Timestamp Latest Date
    [50425.0, 2038, 1, 20], // Day after 32-bit signed Unix Timestamp Latest Date
    [39448.0, 2008, 1, 1],
    [39446.0, 2008, 1, -1],
    [39417.0, 2008, 1, -30],
    [39416.0, 2008, 1, -31],
    [39082.0, 2008, 1, -365],
    [39508.0, 2008, 3, 1],
    [39506.0, 2008, 3, -1],
    [39142.0, 2008, 3, -365],
    [39387.0, 2008, -1, 1],
    [39083.0, 2008, -11, 1],
    [39052.0, 2008, -12, 1],
    [39022.0, 2008, -13, 1],
    [39051.0, 2008, -13, 30],
    [38991.0, 2008, -13, -30],
    [38990.0, 2008, -13, -31],
    [39814.0, 2008, 13, 1],
    [40210.0, 2008, 26, 1],
    [40199.0, 2008, 26, -10],
    [38686.0, 2008, -26, 61],
    [39641.0, 2010, -15, -50],
    [39741.0, 2010, -15, 50],
    [40552.0, 2010, 15, -50],
    [40652.0, 2010, 15, 50],
    [40179.0, 2010, 1.5, 1],
    [40178.0, 2010, 1.5, 0],
    [40148.0, 2010, 0, 1.5],
    [40179.0, 2010, 1, 1.5],
    [41075.0, 2012, 6, 15],
    [3819.0, 10, 6, 15],
    [ExcelError::NAN(), -20, 6, 15],
    [2958465.0, 9999, 12, 31], // Excel maximum date
    [ExcelError::NAN(), 10000, 1, 1], // Exceeded Excel maximum date
    [39670.0, 2008, 8, 10],
    [39813.0, 2008, 12, 31],
    [39692.0, 2008, 8, 32],
    [39844.0, 2008, 13, 31],
    [39813.0, 2009, 1, 0],
    [39812.0, 2009, 1, -1],
    [39782.0, 2009, 0, 0],
    [39781.0, 2009, 0, -1],
    [39752.0, 2009, -1, 0],
    [39751.0, 2009, -1, -1],
    [40146.0, 2010, 0, -1],
    [40329.0, 2010, 5, 31],
    [40199.0, 2010, 1, '21st'], // Excel can't parse ordinal, PhpSpreadsheet can
    [40200.0, 2010, 1, '22nd'], // Excel can't parse ordinal, PhpSpreadsheet can
    [40201.0, 2010, 1, '23rd'], // Excel can't parse ordinal, PhpSpreadsheet can
    [40202.0, 2010, 1, '24th'], // Excel can't parse ordinal, PhpSpreadsheet can
    [40258.0, 2010, 'March', '21st'], // ordinal and month name
    // MS Excel will fail with a #VALUE return, but PhpSpreadsheet can parse this date
    [40258.0, 2010, 'March', 21], // Excel can't parse month name, PhpSpreadsheet can
    [ExcelError::VALUE(), 'ABC', 1, 21],
    [ExcelError::VALUE(), 2010, 'DEF', 21],
    [ExcelError::VALUE(), 2010, 3, 'GHI'],
];
