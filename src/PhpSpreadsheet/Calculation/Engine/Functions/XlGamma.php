<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma;

/**
 * @inheritDoc
 */
class XlGamma extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'GAMMA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Gamma::class, 'gamma'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
