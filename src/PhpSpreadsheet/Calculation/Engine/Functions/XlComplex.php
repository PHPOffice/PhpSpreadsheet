<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;

/**
 * @inheritDoc
 */
class XlComplex extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COMPLEX';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [Complex::class, 'COMPLEX'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
