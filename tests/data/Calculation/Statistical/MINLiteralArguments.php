<?php

declare(strict_types=1);

// Logical values and text representations of numbers typed directly into the argument list are counted;
// text that cannot be translated into a number is an error (unlike text in a reference or an array, which is ignored).
// https://support.microsoft.com/en-us/office/min-function-61635d12-920f-4ce2-a70f-96f202dcc152

return [
    'numeric text counts' => [1, 3, '1'],
    'negative numeric text counts' => [-27, 10, 7, -9, '-27', 2],
    'TRUE counts as 1' => [1, 3, true],
    'FALSE counts as 0' => [0, 3, false],
    'non-numeric text is an error' => ['#VALUE!', 3, 'a'],
    'empty text is an error' => ['#VALUE!', 600000, ''],
];
