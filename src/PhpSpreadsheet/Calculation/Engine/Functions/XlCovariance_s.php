<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

/**
 * @inheritDoc
 */
class XlCovariance_s extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COVARIANCE.S';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Functions::class, 'DUMMY'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
