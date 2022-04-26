<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT;

/**
 * @inheritDoc
 */
class XlTdist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TDIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StudentT::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
