<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;

/**
 * @method DateValue yesterday()
 * @method DateValue today()
 * @method DateValue tomorrow()
 * @method DateValue lastSevenDays()
 * @method DateValue lastWeek()
 * @method DateValue thisWeek()
 * @method DateValue nextWeek()
 * @method DateValue lastMonth()
 * @method DateValue thisMonth()
 * @method DateValue nextMonth()
 */
class DateValue extends WizardAbstract
{
    protected const MAGIC_OPERATIONS = [
        'yesterday' => 'yesterday',
        'today' => 'today',
        'tomorrow' => 'tomorrow',
        'lastSevenDays' => 'last7Days',
        'last7Days' => 'last7Days',
        'lastWeek' => 'lastWeek',
        'thisWeek' => 'thisWeek',
        'nextWeek' => 'nextWeek',
        'lastMonth' => 'lastMonth',
        'thisMonth' => 'thisMonth',
        'nextMonth' => 'nextMonth',
    ];

    private const EXPRESSIONS = [
        'yesterday' => 'FLOOR(%s,1)=TODAY()-1',
        'today' => 'FLOOR(%s,1)=TODAY()',
        'tomorrow' => 'FLOOR(%s,1)=TODAY()+1',
        'last7Days' => 'AND(TODAY()-FLOOR(%s,1)<=6,FLOOR(%s,1)<=TODAY())',
        'lastWeek' => 'AND(TODAY()-ROUNDDOWN(%s,0)>=(WEEKDAY(TODAY())),TODAY()-ROUNDDOWN(%s,0)<(WEEKDAY(TODAY())+7))',
        'thisWeek' => 'AND(TODAY()-ROUNDDOWN(%s,0)<=WEEKDAY(TODAY())-1,ROUNDDOWN(%s,0)-TODAY()<=7-WEEKDAY(TODAY()))',
        'nextWeek' => 'AND(ROUNDDOWN(%s,0)-TODAY()>(7-WEEKDAY(TODAY())),ROUNDDOWN(%s,0)-TODAY()<(15-WEEKDAY(TODAY())))',
        'lastMonth' => 'AND(MONTH(%s)=MONTH(EDATE(TODAY(),0-1)),YEAR(%s)=YEAR(EDATE(TODAY(),0-1)))',
        'thisMonth' => 'AND(MONTH(%s)=MONTH(TODAY()),YEAR(%s)=YEAR(TODAY()))',
        'nextMonth' => 'AND(MONTH(%s)=MONTH(EDATE(TODAY(),0+1)),YEAR(%s)=YEAR(EDATE(TODAY(),0+1)))',
    ];

    /** @var string */
    protected $operator;

    public function __construct(string $cellRange)
    {
        parent::__construct($cellRange);
    }

    protected function operator(string $operator): void
    {
        $this->operator = $operator;
    }

    protected function setExpression(): void
    {
        $referenceCount = substr_count(self::EXPRESSIONS[$this->operator], '%s');
        $references = array_fill(0, $referenceCount, $this->referenceCell);
        $this->expression = sprintf(self::EXPRESSIONS[$this->operator], ...$references);
    }

    public function getConditional()
    {
        $this->setExpression();

        $conditional = new Conditional();
        $conditional->setConditionType(Conditional::CONDITION_TIMEPERIOD);
        $conditional->setText($this->operator);
        $conditional->setConditions($this->expression);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }

    /**
     * @param $methodName
     * @param $arguments
     */
    public function __call($methodName, $arguments)
    {
        if (!isset(self::MAGIC_OPERATIONS[$methodName])) {
            throw new Exception('Invalid Operation for Date Value CF Rule Wizard');
        }

        $this->operator(self::MAGIC_OPERATIONS[$methodName]);

        return $this;
    }
}
