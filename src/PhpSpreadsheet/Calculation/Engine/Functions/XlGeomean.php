<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages\Mean;

/**
 * @inheritDoc
 */
class XlGeomean extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'GEOMEAN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Mean::class, 'geometric'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
