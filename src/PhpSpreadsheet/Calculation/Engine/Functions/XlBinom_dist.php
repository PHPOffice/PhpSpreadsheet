<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial;

/**
 * @inheritDoc
 */
class XlBinom_dist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BINOM.DIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Binomial::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
