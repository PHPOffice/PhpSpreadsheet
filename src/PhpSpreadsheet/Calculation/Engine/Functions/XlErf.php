<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf;

/**
 * @inheritDoc
 */
class XlErf extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ERF';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [Erf::class, 'ERF'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
