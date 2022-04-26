<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate;

/**
 * @inheritDoc
 */
class XlRept extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'REPT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Concatenate::class, 'builtinREPT'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
