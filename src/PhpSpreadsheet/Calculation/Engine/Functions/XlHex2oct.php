<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertHex;

/**
 * @inheritDoc
 */
class XlHex2oct extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'HEX2OCT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ConvertHex::class, 'toOctal'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
