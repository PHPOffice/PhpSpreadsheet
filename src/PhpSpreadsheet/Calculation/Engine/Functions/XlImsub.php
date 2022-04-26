<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexOperations;

/**
 * @inheritDoc
 */
class XlImsub extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IMSUB';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ComplexOperations::class, 'IMSUB'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
