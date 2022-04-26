<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertOctal;

/**
 * @inheritDoc
 */
class XlOct2dec extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'OCT2DEC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertOctal::class, 'toDecimal'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
