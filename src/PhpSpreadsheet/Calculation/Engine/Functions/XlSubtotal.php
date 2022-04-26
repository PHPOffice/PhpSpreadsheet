<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlPassCellReference;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Subtotal;

/**
 * @inheritDoc
 */
class XlSubtotal extends XlFunctionAbstract
{
    use XlPassCellReference;

    /**
     * @var string
     */
    protected $name = 'SUBTOTAL';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Subtotal::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '2+';
}
