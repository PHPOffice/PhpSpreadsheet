<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Variances;

/**
 * @inheritDoc
 */
class XlVar_p extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'VAR.P';

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
