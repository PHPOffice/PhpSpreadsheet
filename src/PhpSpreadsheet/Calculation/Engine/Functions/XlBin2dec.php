<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertBinary;

/**
 * @inheritDoc
 */
class XlBin2dec extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BIN2DEC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertBinary::class, 'toDecimal'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
