<?php

declare(strict_types=1);

// Excel DateTimeStamp  Timezone               Result            Comments
return [
    // 19-Dec-1960 00:00:00 EST => 19-Dec-1960 05:00:00 UTC
    [
        -285_102_000,
        22269,
        'America/New_York',
    ],
    // 01-Jan-1970 00:00:00 EST => 01-Jan-1970 05:00:00 UTC    PHP Base Date
    [
        18000,
        25569,
        'America/New_York',
    ],
    // 07-Dec-1982 00:00:00 EST => 07-Dec-1982 05:00:00 UTC
    [
        408_085_200,
        30292,
        'America/New_York',
    ],
    // 12-Jun-2008 00:00:00 EDT => 12-Jun-2008 04:00:00 UTC
    [
        1_213_243_200,
        39611,
        'America/New_York',
    ],
    // 19-Jan-2038 00:00:00 EST => 19-Jan-2038 05:00:00 UTC    PHP 32-bit Latest Date
    [
        2_147_490_000,
        50424,
        'America/New_York',
    ],
    // 05-Mar-1961 13:37:46 EST => 05-Mar-1961 18:37:46 UTC
    [
        -278_486_534,
        22345.56789,
        'America/New_York',
    ],
    // 05-Mar-1961 16:17:37 EST => 05-Mar-1961 21:17:37 UTC
    [
        -278_476_943,
        22345.6789,
        'America/New_York',
    ],
    // 12:00:00 EST => 17:00:00 UTC
    [
        61200,
        0.5,
        'America/New_York',
    ],
    // 18:00.00 EST => 23:00:00 UTC
    [
        82800,
        0.75,
        'America/New_York',
    ],
    // 02:57:46 EST => 07:57:46 UTC
    [
        28666,
        0.12345,
        'America/New_York',
    ],
    // 02-Nov-2012 00:00:00 EDT => 02-Nov-2012 04:00:00 UTC
    [
        1_351_828_800,
        41215,
        'America/New_York',
    ],
    // 19-Dec-1960 00:00:00 NZST => 18-Dec-1960 12:00:00 UTC
    [
        -285_163_200,
        22269,
        'Pacific/Auckland',
    ],
    // 01-Jan-1970 00:00:00 NZST => 31-Dec-1969 12:00:00 UTC    PHP Base Date
    [
        -43200,
        25569,
        'Pacific/Auckland',
    ],
    // 07-Dec-1982 00:00:00 NZDT => 06-Dec-1982 11:00:00 UTC
    [
        408_020_400,
        30292,
        'Pacific/Auckland',
    ],
    // 12-Jun-2008 00:00:00 NZST => 11-Jun-2008 12:00:00 UTC
    [
        1_213_185_600,
        39611,
        'Pacific/Auckland',
    ],
    // 18-Jan-2038 12:00:00 NZDT => 17-Jan-2038 23:00:00 UTC    PHP 32-bit Latest Date
    [
        2_147_382_000,
        50423.5,
        'Pacific/Auckland',
    ],
    // 05-Mar-1961 13:37:46 NZST => 05-Mar-1961 01:37:46 UTC
    [
        -278_547_734,
        22345.56789,
        'Pacific/Auckland',
    ],
    // 05-Mar-1961 16:17:37 NZST => 05-Mar-1961 04:17:37 UTC
    [
        -278_538_143,
        22345.6789,
        'Pacific/Auckland',
    ],
    // 12:00:00 NZST => 00:00:00 UTC
    [
        0,
        0.5,
        'Pacific/Auckland',
    ],
    // 18:00.00 NZST => 06:00:00 UTC
    [
        21600,
        0.75,
        'Pacific/Auckland',
    ],
    // 02:57:46 NZST => 14:57:46 UTC
    [
        -32534,
        0.12345,
        'Pacific/Auckland',
    ],
    // 02-Nov-2012 00:00:00 NZDT => 01-Nov-2012 11:00:00 UTC
    [
        1_351_767_600,
        41215,
        'Pacific/Auckland',
    ],
];
