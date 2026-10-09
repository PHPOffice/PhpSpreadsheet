<?php

declare(strict_types=1);

return [
    [
        '<"A"',
        '<A',
    ],
    [
        '>"A"',
        '>A',
    ],
    [
        '<="A"',
        '<=A',
    ],
    [
        '>"A"',
        '>A',
    ],
    [
        '>="A"',
        '>=A',
    ],
    [
        '<>"A"',
        '<>A',
    ],
    [
        '<"<A"',
        '<<A',
    ],
    [
        '="A"',
        '=A',
    ],
    [
        '="""A"""',
        '="A"',
    ],
    [
        '="""A""B"""',
        '="A"B"',
    ],
    [
        '<>"< PLEASE SELECT >"',
        '<>< Please Select >',
    ],
    [
        '<>""',
        '<>',
    ],
    [
        '=""',
        '""',
    ],
    'text TRUE matches logical TRUE' => [
        '=TRUE',
        'TRUE',
    ],
    'text true matches logical TRUE' => [
        '=TRUE',
        'true',
    ],
    'text FALSE matches logical FALSE' => [
        '=FALSE',
        'FALSE',
    ],
    'text =TRUE is unchanged' => [
        '=TRUE',
        '=TRUE',
    ],
];
