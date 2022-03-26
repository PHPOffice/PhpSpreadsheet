<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages\Mean;

/**
 * @inheritDoc
 */
class XlHarmean extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'HARMEAN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Mean::class, 'harmonic'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
