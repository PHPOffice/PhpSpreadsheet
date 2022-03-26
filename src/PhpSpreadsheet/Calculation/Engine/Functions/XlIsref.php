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
class XlIsref extends XlFunctionAbstract
{
    use XlPassByReference;
    use XlPassCellReference;

    /**
     * @var string
     */
    protected $name = 'ISREF';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [Value::class, 'isRef'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
