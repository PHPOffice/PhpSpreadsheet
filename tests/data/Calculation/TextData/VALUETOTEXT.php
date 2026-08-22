<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\RichText\RichText;

$richText1 = new RichText();
$richText1->createTextRun('Hello');
$richText1->createText(' World');

$richText2 = new RichText();
$richText2->createTextRun('Hello');
$richText2->createText("\nWorld");

return [
    ['1', 1, 0],
    ['1.23', 1.23, 0],
    ['-123.456', -123.456, 0],
    ['TRUE', true, 0],
    ['FALSE', false, 0],
    ['Hello World', 'Hello World', 0],
    ['HelloWorld', "Hello\nWorld", 0],
    ['"Hello World"', 'Hello World', 1],
    ['"HelloWorld"', "Hello\nWorld", 1],
    ['Hello World', $richText1, 0],
    ['HelloWorld', $richText2, 0],
    ['"Hello World"', $richText1, 1],
    ['"HelloWorld"', $richText2, 1],
];
