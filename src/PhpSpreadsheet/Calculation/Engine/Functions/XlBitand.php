<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;

/**
 * @inheritDoc
 */
class XlBitand extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BITAND';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BitWise::class, 'BITAND'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
