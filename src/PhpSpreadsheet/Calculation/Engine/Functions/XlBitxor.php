<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;

/**
 * @inheritDoc
 */
class XlBitxor extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BITXOR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [BitWise::class, 'BITXOR'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
