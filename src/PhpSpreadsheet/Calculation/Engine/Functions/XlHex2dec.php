<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex;

/**
 * @inheritDoc
 */
class XlHex2dec extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'HEX2DEC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertHex::class, 'toDecimal'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
