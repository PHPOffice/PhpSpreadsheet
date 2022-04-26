<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;

/**
 * @inheritDoc
 */
class XlImaginary extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IMAGINARY';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [Complex::class, 'IMAGINARY'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
