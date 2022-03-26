<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances;

/**
 * @inheritDoc
 */
class XlVarp extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'VARP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Variances::class, 'VARP'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
