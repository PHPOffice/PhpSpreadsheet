# Conditional Formatting

## Introduction

In addition to standard cell formatting options, most spreadsheet software provides an option known as Conditional Formatting, which allows formatting options to be set based on the value of a cell.

The cell's standard formatting defines most style elements that will always be applied, such as the font face and size; but Conditional Formatting allows you to override some elements of that cell style such as number format mask; font colour, bold, italic and underlining; borders and fill colour and pattern.

Conditional Formatting can be applied to individual cells, or to a range of cells.

### Example

As a simple example in MS Excel itself, if we wanted to highlight all cells in the range A1:A10 that contain values greater than 80, start by selecting the range of cells.

![11-01-CF-Simple-Select-Range.png](./images/11-01-CF-Simple-Select-Range.png)

On the Home tab, in the "Styles" group, click "Conditional Formatting". This allows us to select an Excel Wizard to guide us through the process of creating a Conditional Rule and defining a Style for that rule.

![11-02-CF-Simple-Tab.png](./images/11-02-CF-Simple-Tab.png)

Click "Highlight Cells Rules", then "Greater Than".

![11-03-CF-Simple-CellIs-GreaterThan.png](./images/11-03-CF-Simple-CellIs-GreaterThan.png)

Enter the value "80" in the prompt box; and either select one of the pre-defined formatting style (or create a custom style from there).

![11-04-CF-Simple-CellIs-Value-and-Style.png](./images/11-04-CF-Simple-CellIs-Value-and-Style.png)

Then click "OK". The rule is immediately applied to the selected range of cells, highlighting all those with a value greater than 80 in the chosen style.

![11-05-CF-Simple-CellIs-Highlighted.png](./images/11-05-CF-Simple-CellIs-Highlighted.png)

Any change to the value of a cell within that range will immediately check the rule, and automatically apply the new styling if it applies.

![11-06-CF-Simple-Cell-Value-Change.png](./images/11-06-CF-Simple-Cell-Value-Change.png)

If we wanted to set up the same Conditional Formatting rule in PHPSpreadsheet, we would do so using the following code:

```php
$conditional = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
$conditional->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHAN);
$conditional->addCondition(80);
$conditional->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
$conditional->getStyle()->getFill()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);

$conditionalStyles = $spreadsheet->getActiveSheet()->getStyle('A1:A10')->getConditionalStyles();
$conditionalStyles[] = $conditional;

$spreadsheet->getActiveSheet()->getStyle('A1:A10')->setConditionalStyles($conditionalStyles);
```

Depending on the Rules that we might want to apply for a Condition, sometimes an "operator Type" is required, sometimes not (and not all Operator Types are appropriate for all Condition Types); sometimes a "Condition" is required (or even several conditions), sometimes not, and sometimes it must be a specific Excel formula expression. Creating conditions manually requires a good knowledge of when these different properties need to be set, and with what type of values. This isn't something that an end-user developer should be expected to know. 

So - to eliminate this need for complex and arcane knowledge - since PHPSpreadsheet verson 1.22.0 there is also a series of Wizards that can assist with creating Conditional Formatting rules, and which is capable of setting the appropriate operators and conditions for a Conditional Rule:

```php
$wizardFactory = new \PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard('A1:A10');
$wizard = $wizardFactory->newRule(\PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard::CELL_VALUE);
$wizard->greaterThan(80);
$wizard->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
$wizard->getStyle()->getFill()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);

$conditional = $wizard->getConditional();
```
The Wizard knows which operator types match up with condition types, and provides more meaningful method names for operators, and builds expressions when required; and it also works well with an IDE.

---

Note that `$conditionalStyles` is an array: it is possible to apply several conditions to the same range of cells. If we also wanted to highlight values that were less than 10 in the the A1:A10 range, we can add a second style rule.

In Excel, we would do this by selecting the range again, and going through the same process, this time selecting the "Highlight Cells Rules", then "Less Than" from the "Conditional Styles" menu, entering the value "10" in the prompt box, and selecting the appropriate style.

In PHPSpreadsheet, we would do:

```php
$conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
$conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
$conditional2->addCondition(10);
$conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKRED);
$conditional2->getStyle()->getFill()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

$conditionalStyles = $spreadsheet->getActiveSheet()->getStyle('A1:A10')->getConditionalStyles();
$conditionalStyles[] = $conditional2;

$spreadsheet->getActiveSheet()->getStyle('A1:A10')->setConditionalStyles($conditionalStyles);
```
or again, using the Wizard:
```php
$wizardFactory = new \PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard('A1:A10');
$wizard = $wizardFactory->newRule(\PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard::CELL_VALUE);
$wizard->lessThan(10);
$wizard->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
$wizard->getStyle()->getFill()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);

$conditional = $wizard->getConditional();
```

## Wizards

While the Wizards don't simplify defining the Conditional Style itself; they do make it easier to define the conditions (the rules) where that style will be applied. 

![11-07-CF-Wizard.png](./images/11-07-CF-Wizard.png)

The Wizard Factory allows us to retrieve the appropriate Wizard for the CF Rule that we want to apply. Most of those that have already been defined fall under the "Format only cells that contain" category.
MS Excel provides a whole series of different types of rule, each of which has it's own formatting and logic. The Wizards try to replicate this logic and behaviour, similar to Excel's own "Formatting Rule" helper wizard.

MS Excel | Wizard newRule() Type Constant | Wizard Class Name
---|---|---
Cell Value | Wizard::CELL_VALUE | CellValue
Specific Text | Wizard::TEXT_VALUE | TextValue
Dates Occurring | Wizard::DATES_OCCURRING | DateValue
Blanks | Wizard::BLANKS | Blanks
No Blanks | Wizard::NOT_BLANKS | Blanks
Errors | Wizard::ERRORS | Errors 
No Errors | Wizard::NOT_ERRORS | Errors

We instantiate the Wizard Factory, passing in the cell range where we want to apply Conditional Formatting rules; and can then call the `newRule()` method, passing in the type of Conditional Rule that we want to create in order to return the appropriate Wizard:

```php
$wizardFactory = new \PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard('C3:E5');
$wizard = $wizardFactory->newRule(\PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard::CELL_VALUE);
```
You can, of course, instantiate the Wizard that you want directly, rather than using the factory.
```php
$wizard = new \PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard\CellValue('C3:E5');
```

That Wizard then provides methods allowing us to define the rule, setting the operator and the values that we want to compare for that rule.
Note that not all rules require values, or even operators, but the individual Wizards provide whatever is necessary; and this document lists all options available for every Wizard.

### CellValue Wizard

For the `CellValue` Wizard, we always need to provide an operator and a value; and for the "between" and "notBetween" operators, we need to provide two values to specify a range.

Condition Type | Wizard Type | Conditional Operator Type | Wizard Operators | Notes
---|---|---|---|---
Conditional::CONDITION_CELLIS | Wizard::CELL_VALUE | Conditional::OPERATOR_EQUAL | equals()
| | | Conditional::OPERATOR_NOTEQUAL | notEquals()
| | | Conditional::OPERATOR_GREATERTHAN | greaterThan()
| | | Conditional::OPERATOR_GREATERTHANOREQUAL | greaterThanOrEqual()
| | | Conditional::OPERATOR_LESSTHAN | lessThan()
| | | Conditional::OPERATOR_LESSTHANOREQUAL | lessThanOrEqual()
| | | Conditional::OPERATOR_BETWEEN | between()
| | | Conditional::OPERATOR_NOTBETWEEN | notBetween()
| | | | and() | Used to provide the second operand for `between()` and `notBetween() 

A single operator call is required for every rule (except `between()` and `notBetween`, where the Wizard also provides `and()`); and providing a value is mandatory for all operators.
The values that we need to provide for each operator can be numeric, boolean or string literals (even NULL); cell references; or formulae.

So to set the rule using an operator, we would make a call like:
```php
$wizard->lessThan(10);
```
or when setting a `between()` or `notBetween()` rule, we can make use of the fluent interface with the `and()` method to set the range of values:
```php
$wizard->between(-10)->and(10);
```

To retrieve the Conditional, to add it to our `$conditionalStyles` array, we call the Wizard's `getConditional()` method.
```php
$conditional = $wizard->getConditional();
$conditionalStyles = [];
```
or simply
```php
$conditionalStyles[] = $wizard->getConditional();
```

#### Value Types

##### Literals

If the value is a literal (even a string literal), we simply need to pass the value; the Wizard will ensure that strings are correctly quoted when we get the Conditional from the Wizard.

```php
$wizard->equals('Hello World');
```
If you weren't using the Wizard, you would need to remember to wrap this value in quotes yourself (`'"Hello World"'`)  

However, a cell reference or a formula are also string data, so we need to tell the Wizard if the value that we are passing in isn't just a string literal value, but should be treated as a cell reference or as a formula.

##### Cell References

If we want to use the value from cell `H9` in our rule; then we need to pass a value type of `VALUE_TYPE_CELL` to the operator, in addition to the cell reference itself.

![11-08-CF-Absolute-Cell-Reference.png](./images/11-08-CF-Absolute-Cell-Reference.png)

You can find an example that demonstrates this in the [code samples](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/samples/ConditionalFormatting/01_Basic_Comparisons.php#L103 "Conditional Formatting - Basic Comparisons") for the repo.

```php
$wizard->equals('$H$9', Wizard::VALUE_TYPE_CELL);
```
Note that we pass it as an absolute cell reference, "pinned" (with the `$` symbol) for both the row and the column.

In this next example, we need to use relative cell references, so that the comparison will match the value in column `A` against the values in columns `B` and `C` for each row in our range (`A18:A20`); ie, test if the value in `A18` is between the values in `B18` and `C18`, test if the value in `A19` is between the values in `B19` and `C19`, etc.  

![11-09-CF-Relative-Cell-Reference.png](./images/11-09-CF-Relative-Cell-Reference.png)

```php
$wizard->between('$B1', Wizard::VALUE_TYPE_CELL)
    ->and('$C1', Wizard::VALUE_TYPE_CELL)
    ->setStyle($greenStyle);
```

This example can also be found in the [code samples](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/samples/ConditionalFormatting/01_Basic_Comparisons.php#L126 "Conditional Formatting - Basic Comparisons") for the repo.

In this case, we "pin" the column for the address; but leave the row "unpinned".
Notice also that we treat the first cell in our range as cell `A1`: the relative row number will be adjusted automatically to match each row in our defined range; that is, the range that we specified when we instantiated the Wizard, passed in through the Wizard Factory.

##### Formulae

```php
$wizard->equals('AVERAGE($B1:$C1)', Wizard::VALUE_TYPE_FORMULA);
```

### TextValue Wizard

For the `TextValue` Wizard, we always need to provide an operator and a value.

Condition Type | Wizard Type | Conditional Operator Type | Wizard Operators | Notes
---|---|---|---|---
Conditional::CONDITION_CONTAINSTEXT | Wizard::TEXT_VALUE | Conditional::OPERATOR_CONTAINSTEXT | contains()
Conditional::CONDITION_NOTCONTAINSTEXT | Wizard::TEXT_VALUE | Conditional::OPERATOR_NOTCONTAINS | doesNotContain()
| | |  | doesntContain() | synonym for `doesNotContain()`
Conditional::CONDITION_BEGINSWITH | Wizard::TEXT_VALUE | Conditional::OPERATOR_BEGINSWITH | beginsWith()
| | |  | startsWith() | synonym for `beginsWith()`
Conditional::CONDITION_ENDSWITH | Wizard::TEXT_VALUE | Conditional::OPERATOR_ENDSWITH | endsWith()

The Conditional actually uses a separate "Condition Type" for each, each with its own "Operator Type", and the condition should be an Excel formula (not simply the string value to check), and with a custom `text` attribute. The Wizard should make it a lot easier to create these condition rules.

Conditional Type | Condition Expression
---|---
Conditional::CONDITION_CONTAINSTEXT | NOT(ISERROR(SEARCH(`<value>`,`<cell address>`)))
Conditional::CONDITION_NOTCONTAINSTEXT | ISERROR(SEARCH(`<value>`,`<cell address>`))</
Conditional::CONDITION_BEGINSWITH | LEFT(`<cell address>`,LEN(`<value>`))=`<value>`
Conditional::CONDITION_ENDSWITH | RIGHT(`<cell address>`,LEN(`<value>`))=`<value>`

The `<cell address>` always references the top-left cell in the range of cells for this Conditional Formatting Rule.
The `<value>` should be wrapped in double quotes (`"`) for string literal; unquoted if it is a value stored in a cell reference, or a formula. 

### DateValue Wizard

For the `DateValue` Wizard, we always need to provide an operator; but no value is required.

Condition Type | Wizard Type | Conditional Operator Type | Wizard Operators | Notes
---|---|---|---|---
Conditional::CONDITION_TIMEPERIOD | Wizard::DATES_OCCURRING | Conditional::TIMEPERIOD_TODAY | today()
| | | Conditional::TIMEPERIOD_YESTERDAY | yesterday()
| | | Conditional::TIMEPERIOD_TOMORROW | tomorrow()
| | | Conditional::TIMEPERIOD_LAST_7_DAYS | last7Days()
| | |  | lastSevenDays() | synonym for `last7Days()`
| | | Conditional::TIMEPERIOD_LAST_WEEK | lastWeek()
| | | Conditional::TIMEPERIOD_THIS_WEEK | thisWeek()
| | | Conditional::TIMEPERIOD_NEXT_WEEK | nextWeek()
| | | Conditional::TIMEPERIOD_LAST_MONTH | lastMonth()
| | | Conditional::TIMEPERIOD_THIS_MONTH | thisMonth()
| | | Conditional::TIMEPERIOD_NEXT_MONTH | nextMonth()

The Conditional has no actual "Operator Type", and the condition should be an Excel formula, and with a custom `timePeriod` attribute. The Wizard should make it a lot easier to create these condition rules.

timePeriod Attribute | Condition Expression
---|---
today | FLOOR(`<cell address>`,1)=TODAY()-1
yesterday | FLOOR(`<cell address>`,1)=TODAY()
tomorrow | FLOOR(`<cell address>`,1)=TODAY()+1
last7Days | AND(TODAY()-FLOOR(`<cell address>`,1)<=6,FLOOR(`<cell address>`,1)<=TODAY())
lastWeek | AND(TODAY()-ROUNDDOWN(`<cell address>`,0)>=(WEEKDAY(TODAY())),TODAY()-ROUNDDOWN(`<cell address>`,0)<(WEEKDAY(TODAY())+7))
thisWeek | AND(TODAY()-ROUNDDOWN(`<cell address>`,0)<=WEEKDAY(TODAY())-1,ROUNDDOWN(`<cell address>`,0)-TODAY()<=7-WEEKDAY(TODAY())) 
nextWeek | AND(ROUNDDOWN(`<cell address>`,0)-TODAY()>(7-WEEKDAY(TODAY())),ROUNDDOWN(`<cell address>`,0)-TODAY()<(15-WEEKDAY(TODAY())))
lastMonth | AND(MONTH(`<cell address>`)=MONTH(EDATE(TODAY(),0-1)),YEAR(`<cell address>`)=YEAR(EDATE(TODAY(),0-1)))
thisMonth | AND(MONTH(`<cell address>`)=MONTH(TODAY()),YEAR(`<cell address>`)=YEAR(TODAY()))
nextMonth | AND(MONTH(`<cell address>`)=MONTH(EDATE(TODAY(),0+1)),YEAR(`<cell address>`)=YEAR(EDATE(TODAY(),0+1)))

The `<cell address>` always references the top-left cell in the range of cells for this Conditional Formatting Rule.

### Blanks Wizard

This Wizard is used to define a simple boolean state rule, to determine whether a cell is blank or not blank.
Whether created using the Wizard Factory with a rule type of `Wizard::BLANKS` or `Wizard::NOT_BLANKS`, the same `Blanks` Wizard is returned.
The only difference is that in the one case the rule state is pre-set for `CONDITION_CONTAINSBLANKS`, in the other it is pre-set for `CONDITION_NOTCONTAINSBLANKS`.
However, you can switch between the two rules using the `isBlank()` and `notBlank()` methods; and it is only at the point when you call `getConditional()` that a Conditional will be returned based on the current state of the Wizard. 

Condition Type | Wizard Type | Conditional Operator Type | Wizard Operators | Notes
---|---|---|---|---
Conditional::CONDITION_CONTAINSBLANKS | Wizard::BLANKS | - | isBlank() | Default state
| | | | notBlank()
Conditional::CONDITION_NOTCONTAINSBLANKS | Wizard::NOT_BLANKS | - | notBlank()| Default state
| | | | isBlank()

The following code shows the same Blanks Wizard being used to create both Blank and Non-Blank Conditionals, using a pre-defined `$redStyle` Style object for Blanks, and a pre-defined `$greenStyle` Style object for Non-Blanks.  
```php
$cellRange = 'A2:B3';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\Blanks $blanksWizard */
$blanksWizard = $wizardFactory->newRule(Wizard::BLANKS);

$blanksWizard->setStyle($redStyle);
$conditionalStyles[] = $blanksWizard->getConditional();

$blanksWizard->notBlank()
    ->setStyle($greenStyle);
$conditionalStyles[] = $blanksWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($blanksWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);
```
This example can also be found in the [code samples](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/samples/ConditionalFormatting/03_Blank_Comparisons.php#L58 "Conditional Formatting - Blank Comparisons") for the repo.

![11-10-CF-Blanks-Example.png](./images/11-10-CF-Blanks-Example.png)

No operand/value is required for the Blanks Wizard methods; but the Conditional that is returned contains a defined expression with an Excel formula: 

Blanks Type | Condition Expression
---|---
isBlank() | LEN(TRIM(`<cell address>`))=0
notBlank() | LEN(TRIM(`<cell address>`))>0

The `<cell address>` always references the top-left cell in the range of cells for this Conditional Formatting Rule.

### Errors Wizard

Like the `Blanks` Wizard, this Wizard is used to define a simple boolean state rule, to determine whether a cell with a formula value results in an error or not.
Whether created using the Wizard Factory with a rule type of `Wizard::ERRORS` or `Wizard::NOT_ERRORS`, the same `Errors` Wizard is returned.
The only difference is that in the one case the rule state is pre-set for `CONDITION_CONTAINSERRORS`, in the other it is pre-set for `CONDITION_NOTCONTAINSERRORS`.
However, you can switch between the two rules using the `isError()` and `notError()` methods; and it is only at the point when you call `getConditional()` that a Conditional will be returned based on the current state of the Wizard.

Condition Type | Wizard Type | Conditional Operator Type | Wizard Operators | Notes
---|---|---|---|---
Conditional::CONDITION_CONTAINSERRORS | Wizard::ERRORS | - | isError() | Default state
| | | | notError()
Conditional::CONDITION_NOTCONTAINSERRORS | Wizard::NOT_ERRORS | - | notError()| Default state
| | | | isError()

The following code shows the same Errors Wizard being used to create both Error and Non-Error Conditionals, using a pre-defined `$redStyle` Style object for Errors, and a pre-defined `$greenStyle` Style object for Non-Errors.
```php
$cellRange = 'C2:C6';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\Errors $errorsWizard */
$errorsWizard = $wizardFactory->newRule(Wizard::ERRORS);

$errorsWizard->setStyle($redStyle);
$conditionalStyles[] = $errorsWizard->getConditional();

$errorsWizard->notError()
    ->setStyle($greenStyle);
$conditionalStyles[] = $errorsWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($errorsWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);
```

This example can also be found in the [code samples](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/samples/ConditionalFormatting/04_Error_Comparisons.php#L62 "Conditional Formatting - Error Comparisons") for the repo.

![11-11-CF-Errors-Example.png](./images/11-11-CF-Errors-Example.png)

No operand/value is required for the Errors Wizard methods; but the Conditional that is returned contains a defined expression with an Excel formula:

Blanks Type | Condition Expression
---|---
isError() | ISERROR(`<cell address>`)
notError() | NOT(ISERROR(`<cell address>`))

The `<cell address>` always references the top-left cell in the range of cells for this Conditional Formatting Rule.

### Expression Wizard

