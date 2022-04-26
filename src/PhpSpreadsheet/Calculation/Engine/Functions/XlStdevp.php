<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;

/**
 * @inheritDoc
 */
class XlStdevp extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'STDEVP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StandardDeviations::class, 'STDEVP'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
