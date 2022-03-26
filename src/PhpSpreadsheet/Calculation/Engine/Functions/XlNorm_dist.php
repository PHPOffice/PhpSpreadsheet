<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Normal;

/**
 * @inheritDoc
 */
class XlNorm_dist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NORM.DIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Normal::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
