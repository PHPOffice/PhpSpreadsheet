<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;

/**
 * @inheritDoc
 */
class XlBitlshift extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BITLSHIFT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BitWise::class, 'BITLSHIFT'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
