<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;

/**
 * @inheritDoc
 */
class XlImpower extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IMPOWER';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [ComplexFunctions::class, 'IMPOWER'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
