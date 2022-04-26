<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma;

/**
 * @inheritDoc
 */
class XlGammaln_precise extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'GAMMALN.PRECISE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Gamma::class, 'ln'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
