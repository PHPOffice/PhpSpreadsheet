<?php

declare(strict_types=1);

return [
    'both boolean true' => [true, 'true, true'],
    'boolean and string-true' => [true, 'true, "true"'],
    'true and non-boolean-string' => [true, 'true, "xyz"'],
    'non-boolean-string and true' => [true, '"xyz", true'],
    'empty-string and true' => [true, '"", true'],
    'non-boolean-string and false' => [false, 'false, "xyz"'],
    'false and non-boolean-string' => [false, '"xyz", false'],
    'only non-boolean-string' => ['#VALUE!', '"xyz"'],
    'only boolean-string' => [true, '"true"'],
    'numeric-true and true' => [true, '3.1, true, true'],
    'numeric-false and true' => [false, '0, true, true'],
    'mixed boolean' => [false, 'true, true, true, false'],
    'only true' => [true, 'true'],
    'only false' => [false, 0.0],
    'boolean in array' => [true, '{true}'],
    'boolean-string in array' => ['#VALUE!', '{"true"}'],
];
