<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Confidence;

/**
 * @inheritDoc
 */
class XlConfidence extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CONFIDENCE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Confidence::class, 'CONFIDENCE'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
