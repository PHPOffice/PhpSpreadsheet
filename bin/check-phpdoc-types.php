#!/usr/bin/env php
<?php

/**
 * This will check that the "current patch" does not add or modify lines that contain types as PHPDoc when we can express
 * them with PHP native types. The "current patch" is either the file about to be committed, the non-committed file, or
 * the latest commit.
 *
 * This will help us slowly migrate away from PHPDoc typing to PHP native typing.
 */
function checkPhpDocTypes(): void
{
    $content = shell_exec('git diff --cached') ?? shell_exec('git diff') ?? shell_exec('git show HEAD');
    preg_match_all('~^\+ +\* @(param|var) (mixed|string|int|float|bool|null|array|\?|\|)+( \$\w+)?$~m', "$content", $parameters);
    preg_match_all('~^\+ +\* @return (mixed|string|int|float|bool|null|array|void|\?|\|)+$~m', "$content", $returns);

    $errors = [
        ...$parameters[0],
        ...$returns[0],
    ];

    if ($errors) {
        echo 'PHP native types must be used instead of PHPDoc types (without comments), for the following lines:' . PHP_EOL . PHP_EOL;
        echo implode(PHP_EOL, $errors) . PHP_EOL;
        exit(1);
    }
}

checkPhpDocTypes();
