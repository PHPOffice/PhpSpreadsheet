<?php

return [
    'both boolean true' => [false, 'true, true'],
    'boolean xor string-true' => [false, 'true, "true"'],
    'true xor non-boolean-string' => [true, 'true, "xyz"'],
    'non-boolean-string xor true' => [true, '"xyz", true'],
    'empty-string xor true' => [true, '"", true'],
    'non-boolean-string xor false' => [false, 'false, "xyz"'],
    'false xor non-boolean-string' => [false, '"xyz", false'],
    'only non-boolean-string' => ['#VALUE!', '"xyz"'],
    'only boolean-string' => [true, '"true"'],
    'numeric-true xor true xor true' => [true, '3.1, true, true'],
    'numeric-false xor true xor true' => [false, '0, true, true'],
    'mixed boolean' => [true, 'true, true, true, false'],
    'only true' => [true, 'true'],
    'only false' => [false, 0.0],
    'boolean in array' => [true, '{true}'],
    'boolean-string in array' => ['#VALUE!', '{"true"}'],
];
