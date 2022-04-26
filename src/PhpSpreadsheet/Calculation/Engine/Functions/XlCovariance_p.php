<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends;

/**
 * @inheritDoc
 */
class XlCovariance_p extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COVARIANCE.P';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Trends::class, 'COVAR'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
