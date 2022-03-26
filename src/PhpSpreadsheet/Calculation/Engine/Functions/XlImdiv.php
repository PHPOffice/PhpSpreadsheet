<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations;

/**
 * @inheritDoc
 */
class XlImdiv extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IMDIV';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ComplexOperations::class, 'IMDIV'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
