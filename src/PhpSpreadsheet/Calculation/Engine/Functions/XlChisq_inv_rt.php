<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared;

/**
 * @inheritDoc
 */
class XlChisq_inv_rt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CHISQ.INV.RT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [ChiSquared::class, 'inverseRightTail'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
