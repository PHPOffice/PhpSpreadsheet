<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
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
class DateValue extends WizardAbstract implements WizardInterface
{
    protected const MAGIC_OPERATIONS = [
        'yesterday' => Conditional::TIMEPERIOD_YESTERDAY,
        'today' => Conditional::TIMEPERIOD_TODAY,
        'tomorrow' => Conditional::TIMEPERIOD_TOMORROW,
        'lastSevenDays' => Conditional::TIMEPERIOD_LAST_7_DAYS,
        'last7Days' => Conditional::TIMEPERIOD_LAST_7_DAYS,
        'lastWeek' => Conditional::TIMEPERIOD_LAST_WEEK,
        'thisWeek' => Conditional::TIMEPERIOD_THIS_WEEK,
        'nextWeek' => Conditional::TIMEPERIOD_NEXT_WEEK,
        'lastMonth' => Conditional::TIMEPERIOD_LAST_MONTH,
        'thisMonth' => Conditional::TIMEPERIOD_THIS_MONTH,
        'nextMonth' => Conditional::TIMEPERIOD_NEXT_MONTH,
    ];

    protected const EXPRESSIONS = [
        Conditional::TIMEPERIOD_YESTERDAY => 'FLOOR(%s,1)=TODAY()-1',
        Conditional::TIMEPERIOD_TODAY => 'FLOOR(%s,1)=TODAY()',
        Conditional::TIMEPERIOD_TOMORROW => 'FLOOR(%s,1)=TODAY()+1',
        Conditional::TIMEPERIOD_LAST_7_DAYS => 'AND(TODAY()-FLOOR(%s,1)<=6,FLOOR(%s,1)<=TODAY())',
        Conditional::TIMEPERIOD_LAST_WEEK => 'AND(TODAY()-ROUNDDOWN(%s,0)>=(WEEKDAY(TODAY())),TODAY()-ROUNDDOWN(%s,0)<(WEEKDAY(TODAY())+7))',
        Conditional::TIMEPERIOD_THIS_WEEK => 'AND(TODAY()-ROUNDDOWN(%s,0)<=WEEKDAY(TODAY())-1,ROUNDDOWN(%s,0)-TODAY()<=7-WEEKDAY(TODAY()))',
        Conditional::TIMEPERIOD_NEXT_WEEK => 'AND(ROUNDDOWN(%s,0)-TODAY()>(7-WEEKDAY(TODAY())),ROUNDDOWN(%s,0)-TODAY()<(15-WEEKDAY(TODAY())))',
        Conditional::TIMEPERIOD_LAST_MONTH => 'AND(MONTH(%s)=MONTH(EDATE(TODAY(),0-1)),YEAR(%s)=YEAR(EDATE(TODAY(),0-1)))',
        Conditional::TIMEPERIOD_THIS_MONTH => 'AND(MONTH(%s)=MONTH(TODAY()),YEAR(%s)=YEAR(TODAY()))',
        Conditional::TIMEPERIOD_NEXT_MONTH => 'AND(MONTH(%s)=MONTH(EDATE(TODAY(),0+1)),YEAR(%s)=YEAR(EDATE(TODAY(),0+1)))',
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

    public function getConditional(): Conditional
    {
        $this->setExpression();

        $conditional = new Conditional();
        $conditional->setConditionType(Conditional::CONDITION_TIMEPERIOD);
        $conditional->setText($this->operator);
        $conditional->setConditions([$this->expression]);
        $conditional->setStyle($this->getStyle());
        $conditional->setStopIfTrue($this->getStopIfTrue());

        return $conditional;
    }

    public static function fromConditional(Conditional $conditional, string $cellRange = 'A1'): WizardInterface
    {
        if ($conditional->getConditionType() !== Conditional::CONDITION_TIMEPERIOD) {
            throw new Exception('Conditional is not a Date Value CF Rule conditional');
        }

        $wizard = new self($cellRange);
        $wizard->style = $conditional->getStyle();
        $wizard->stopIfTrue = $conditional->getStopIfTrue();
        $wizard->operator = $conditional->getText();

        return $wizard;
    }

    /**
     * @param string $methodName
     * @param mixed[] $arguments
     */
    public function __call($methodName, $arguments): self
    {
        if (!isset(self::MAGIC_OPERATIONS[$methodName])) {
            throw new Exception('Invalid Operation for Date Value CF Rule Wizard');
        }

        $this->operator(self::MAGIC_OPERATIONS[$methodName]);

        return $this;
    }
}
