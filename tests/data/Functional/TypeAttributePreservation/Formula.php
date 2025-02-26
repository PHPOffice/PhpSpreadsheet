<?php

declare(strict_types=1);

return [
    [
        ['string'],
    ],
    [
        ['="string"'],
    ],
    [
        [1],
    ],
    [
        [0],
    ],
    [
        [true],
    ],
    [
        [false],
    ],
    [
        ['=TRUE()'],
    ],
    [
        ['=ISFORMULA(B1)', '=1+2'],
    ],
    [
        ['1'],
    ],
    [
        ['0'],
    ],
    [
        ['null'],
    ],
    [
        [null],
    ],
    'issue3568' => [
        ['="00"&B1', '123'],
    ],
    'unimplemented function' => [
        ['=INFO("SYSTEM")'],
    ],
];
