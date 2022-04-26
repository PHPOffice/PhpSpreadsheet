<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles;

/**
 * @inheritDoc
 */
class XlRank_eq extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RANK.EQ';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Percentiles::class, 'RANK'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
