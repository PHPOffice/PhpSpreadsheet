<?php

declare(strict_types=1);

return [
    [
        "1, 2, 3, a, b, c, TRUE, FALSE, #DIV/0!, 44774, 1, 44777, 12345.6789, -2.4, Hello\nWorld",
        [
            [1, 2, 3],
            ['a', 'b', 'c'],
            [true, false, '=12/0'],
            ['=DATE(2022,8,1)', '1', '=A4+3'],
            [12345.6789, '=-12/5', "Hello\nWorld"],
        ],
        0,
    ],
    [
        "{1,2,3;\"a\",\"b\",\"c\";TRUE,FALSE,#DIV/0!;44774,1,44777;12345.6789,-2.4,\"Hello\nWorld\"}",
        [
            [1, 2, 3],
            ['a', 'b', 'c'],
            [true, false, '=12/0'],
            ['=DATE(2022,8,1)', 1, '=A4+3'],
            [12345.6789, '=-12/5', "Hello\nWorld"],
        ],
        1,
    ],
];
