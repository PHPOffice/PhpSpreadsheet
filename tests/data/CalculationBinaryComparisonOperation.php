<?php

declare(strict_types=1);

//  formula, expectedResultExcel, expectedResultOpenOffice

return [
    [
        '=TRUE',
        true,
        true,
    ],
    [
        '=1 + 2.5',
        3.5,
        3.5,
    ],
    [
        '=2.5 + 1',
        3.5,
        3.5,
    ],
    [
        '=1 - 2.5',
        -1.5,
        -1.5,
    ],
    [
        '=2.5 - 1',
        1.5,
        1.5,
    ],
    [
        '=3 > 1',
        true,
        true,
    ],
    [
        '=3 > 3',
        false,
        false,
    ],
    [
        '=1 > 3',
        false,
        false,
    ],
    [
        '=3 < 1',
        false,
        false,
    ],
    [
        '=3 < 3',
        false,
        false,
    ],
    [
        '=1 < 3',
        true,
        true,
    ],
    [
        '=3 = 1',
        false,
        false,
    ],
    [
        '=3 = 3',
        true,
        true,
    ],
    [
        '=1 = 1.0',
        true,
        true,
    ],
    [
        '=3 >= 1',
        true,
        true,
    ],
    [
        '=3 >= 3',
        true,
        true,
    ],
    [
        '=1 >= 3',
        false,
        false,
    ],
    [
        '=3 <= 1',
        false,
        false,
    ],
    [
        '=3 <= 3',
        true,
        true,
    ],
    [
        '=1 <= 3',
        true,
        true,
    ],
    [
        '=3 <> 1',
        true,
        true,
    ],
    [
        '=3 <> 3',
        false,
        false,
    ],
    [
        '=1 <> 1.0',
        false,
        false,
    ],
    [
        '="a" > "a"',
        false,
        false,
    ],
    [
        '="A" > "A"',
        false,
        false,
    ],
    [
        '="A" > "a"',
        false,
        true,
    ],
    [
        '="a" > "A"',
        false,
        false,
    ],
    [
        '="a" < "a"',
        false,
        false,
    ],
    [
        '="A" < "A"',
        false,
        false,
    ],
    [
        '="A" < "a"',
        false,
        false,
    ],
    [
        '="a" < "A"',
        false,
        true,
    ],
    [
        '="a" = "a"',
        true,
        true,
    ],
    [
        '="A" = "A"',
        true,
        true,
    ],
    [
        '="A" = "a"',
        true,
        false,
    ],
    [
        '="a" = "A"',
        true,
        false,
    ],
    [
        '="a" <= "a"',
        true,
        true,
    ],
    [
        '="A" <= "A"',
        true,
        true,
    ],
    [
        '="A" <= "a"',
        true,
        false,
    ],
    [
        '="a" <= "A"',
        true,
        true,
    ],
    [
        '="a" >= "a"',
        true,
        true,
    ],
    [
        '="A" >= "A"',
        true,
        true,
    ],
    [
        '="A" >= "a"',
        true,
        true,
    ],
    [
        '="a" >= "A"',
        true,
        false,
    ],
    [
        '="a" <> "a"',
        false,
        false,
    ],
    [
        '="A" <> "A"',
        false,
        false,
    ],
    [
        '="A" <> "a"',
        false,
        true,
    ],
    [
        '="a" <> "A"',
        false,
        true,
    ],
    [
        '= NULL = 0',
        true,
        true,
    ],
    [
        '= NULL < 0',
        false,
        false,
    ],
    [
        '= NULL <= 0',
        true,
        true,
    ],
    [
        '= NULL > 0',
        false,
        false,
    ],
    [
        '= NULL >= 0',
        true,
        true,
    ],
    [
        '= NULL <> 0',
        false,
        false,
    ],
    [
        '="A" > "b"',
        false,
        true,
    ],
    [
        '="a" > "b"',
        false,
        false,
    ],
    [
        '="b" > "a"',
        true,
        true,
    ],
    [
        '="b" > "A"',
        true,
        false,
    ],
    // Test natural sorting is not used
    [
        '="a2" > "a10"',
        true,
        true,
    ],
];
