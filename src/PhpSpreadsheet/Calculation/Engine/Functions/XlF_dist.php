<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

/**
 * @inheritDoc
 */
class XlF_dist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'F.DIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [F::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
