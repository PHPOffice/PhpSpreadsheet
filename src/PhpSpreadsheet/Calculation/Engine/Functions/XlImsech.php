<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;

/**
 * @inheritDoc
 */
class XlImsech extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IMSECH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ComplexFunctions::class, 'IMSECH'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
