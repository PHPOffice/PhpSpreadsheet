<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlPassByReference;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlPassCellReference;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;

/**
 * @inheritDoc
 */
class XlIsformula extends XlFunctionAbstract
{
    use XlPassByReference;
    use XlPassCellReference;

    /**
     * @var string
     */
    protected $name = 'ISFORMULA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [Value::class, 'isFormula'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
