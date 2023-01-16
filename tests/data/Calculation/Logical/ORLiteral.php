<?php

return [
    'both boolean true' => [true, 'true, true'],
    'boolean or string-true' => [true, 'true, "true"'],
    'true or non-boolean-string' => [true, 'true, "xyz"'],
    'non-boolean-string or true' => [true, '"xyz", true'],
    'empty-string or true' => [true, '"", true'],
    'non-boolean-string or false' => [false, 'false, "xyz"'],
    'false or non-boolean-string' => [false, '"xyz", false'],
    'only non-boolean-string' => ['#VALUE!', '"xyz"'],
    'only boolean-string' => [true, '"true"'],
    'numeric-true or true' => [true, '3.1, true, true'],
    'numeric-false or true' => [true, '0, true, true'],
    'mixed boolean' => [true, 'true, true, true, false'],
    'only true' => [true, 'true'],
    'only false' => [false, 0.0],
    'boolean in array' => [true, '{true}'],
    'boolean-string in array' => ['#VALUE!', '{"true"}'],
];
