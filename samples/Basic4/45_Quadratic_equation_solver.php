<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

require __DIR__ . '/../Header.php';
/** @var Sample $helper */
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}
$postA = htmlentities(StringHelper::convertToString($_POST['A'] ?? ''));
$postB = htmlentities(StringHelper::convertToString($_POST['B'] ?? ''));
$postC = htmlentities(StringHelper::convertToString($_POST['C'] ?? ''));
?>
<form action="45_Quadratic_equation_solver.php" method="POST">
    Enter the coefficients for Ax<sup>2</sup> + Bx + C = 0
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <b>A&nbsp;</b>
            </td>
            <td>
                <input name="A" type="text" size="8" value="<?php echo $postA; ?>">
            </td>
        </tr>
        <tr>
            <td>
                <b>B&nbsp;</b>
            </td>
            <td>
                <input name="B" type="text" size="8" value="<?php echo $postB; ?>">
            </td>
        </tr>
        <tr>
            <td><b>C&nbsp;</b>
            </td>
            <td>
                <input name="C" type="text" size="8" value="<?php echo $postC; ?>">
            </td>
        </tr>
    </table>
    <input name="submit" type="submit" value="calculate"><br />
    If A=0, the equation is not quadratic.
</form>

<?php
/**     If the user has submitted the form, then we need to execute a calculation * */
if (isset($_POST['submit'])) {
    if (!is_numeric($postA) || !is_numeric($postB) || !is_numeric($postC)) { // validate input
        $helper->log('Non-numeric input');
    } elseif ($postA == 0) {
        $helper->log('The equation is not quadratic');
    } else {
        // Calculate and Display the results
        $helper->log('<hr /><b>Roots:</b><br />');

        $discriminantFormula = '=POWER(' . $postB . ',2) - (4 * ' . $postA . ' * ' . $postC . ')';
        $discriminant = Calculation::getInstance()->calculateFormula($discriminantFormula);
        $discriminant = StringHelper::convertToString($discriminant);

        $r1Formula = '=IMDIV(IMSUM(-' . $postB . ',IMSQRT(' . $discriminant . ')),2 * ' . $postA . ')';
        $r2Formula = '=IF(' . $discriminant . '=0,"Only one root",IMDIV(IMSUB(-' . $postB . ',IMSQRT(' . $discriminant . ')),2 * ' . $postA . '))';

        /** @var string */
        $output = Calculation::getInstance()->calculateFormula($r1Formula);
        $helper->log("$output");
        /** @var string */
        $output = Calculation::getInstance()->calculateFormula($r2Formula);
        $helper->log("$output");
        $callEndTime = microtime(true);
        $helper->logEndingNotes();
    }
}
