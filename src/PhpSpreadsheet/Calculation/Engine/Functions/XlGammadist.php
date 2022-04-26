<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Gamma;

/**
 * @inheritDoc
 */
class XlGammadist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'GAMMADIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Gamma::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
