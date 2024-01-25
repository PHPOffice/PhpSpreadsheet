<?php

declare(strict_types=1);

// Excel DateTimeStamp        Result            Comments

return [
    [
        -1_956_528_000,
        1462,
    ],
    [
        -1_956_441_600,
        1463,
    ],
    [
        -158_803_200,
        22269,
    ],
    [
        126_316_800,
        25569,
    ],
    [
        534_384_000,
        30292,
    ],
    [
        1_339_545_600,
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
        2_156_112_000,
        49062,
    ],
];
