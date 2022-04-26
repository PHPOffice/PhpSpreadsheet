<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\StandardDeviations;

/**
 * @inheritDoc
 */
class XlStdev_s extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'STDEV.S';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StandardDeviations::class, 'STDEV'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
