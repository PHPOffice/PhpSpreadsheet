<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Erf;

/**
 * @inheritDoc
 */
class XlErf_precise extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ERF.PRECISE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_ENGINEERING;

    /**
     * @var callable
     */
    protected $functionCall = [Erf::class, 'ERFPRECISE'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
