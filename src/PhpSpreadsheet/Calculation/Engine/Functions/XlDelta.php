<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Compare;

/**
 * @inheritDoc
 */
class XlDelta extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DELTA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [Compare::class, 'DELTA'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
