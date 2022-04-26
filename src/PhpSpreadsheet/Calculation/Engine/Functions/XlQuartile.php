<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles;

/**
 * @inheritDoc
 */
class XlQuartile extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'QUARTILE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Percentiles::class, 'QUARTILE'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
