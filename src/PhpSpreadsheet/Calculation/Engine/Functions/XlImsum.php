<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations;

/**
 * @inheritDoc
 */
class XlImsum extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IMSUM';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ComplexOperations::class, 'IMSUM'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
