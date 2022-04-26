<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\LogNormal;

/**
 * @inheritDoc
 */
class XlLognorm_dist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'LOGNORM.DIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [LogNormal::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
