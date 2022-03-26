<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertDecimal;

/**
 * @inheritDoc
 */
class XlDec2hex extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DEC2HEX';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertDecimal::class, 'toHex'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
