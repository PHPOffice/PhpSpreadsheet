<?php

declare(strict_types=1);

// Excel DateTimeStamp        Result            Comments

return [
    [
        -1956528000,
        1462,
    ],
    [
        -1956441600,
        1463,
    ],
    [
        -158803200,
        22269,
    ],
    [
        126316800,
        25569,
    ],
    [
        534384000,
        30292,
    ],
    [
        1339545600,
        39611,
    ],
    // 06:00:00
    [
        gmmktime(6, 0, 0, 1, 1, 1904), // 32-bit safe - no Y2038 problem
        0.25,
    ],
    // 08:00.00
    [
        gmmktime(8, 0, 0, 1, 1, 1904), // 32-bit safe - no Y2038 problem
        0.3333333333333333333,
    ],
    // 13:02:13
    [
        gmmktime(13, 2, 13, 1, 1, 1904), // 32-bit safe - no Y2038 problem
        0.54321,
    ],
    // 29-Apr-2038 00:00:00 beyond PHP 32-bit Latest Date
    [
        2156112000,
        49062,
    ],
];
