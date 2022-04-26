<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\ChiSquared;

/**
 * @inheritDoc
 */
class XlChidist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CHIDIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [ChiSquared::class, 'distributionRightTail'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
