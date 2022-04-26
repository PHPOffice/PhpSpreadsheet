<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial;

/**
 * @inheritDoc
 */
class XlNegbinomdist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NEGBINOMDIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Binomial::class, 'negative'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
