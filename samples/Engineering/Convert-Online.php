<?php

use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

require __DIR__ . '/../Header.php';
/** @var Sample $helper */
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}
$post = [];
foreach (['category', 'quantity', 'fromUnit', 'toUnit'] as $value) {
    if (isset($_POST[$value])) {
        $post[$value] = StringHelper::convertToString($_POST[$value]);
    }
}
$categories = ConvertUOM::getConversionCategories();
$defaultCategory = $post['category'] ?? $categories[0];
$units = [];
foreach ($categories as $category) {
    $categoryUnits = ConvertUOM::getConversionCategoryUnitDetails($category)[$category];
    $categoryUnits = array_unique(
        array_combine(
            array_column($categoryUnits, 'unit'),
            array_column($categoryUnits, 'description')
        )
    );
    $units[$category] = $categoryUnits;
}

?>
<form action=Convert-Online.php method="POST">
    <div class="mb-3 row">
        <label for="category" class="col-sm-2 col-form-label">Category</label>
        <div class="col-sm-10">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <?php foreach ($categories as $category) {
                    echo "<option value=\"{$category}\" " . ((isset($post['category']) && $post['category'] === $category) ? 'selected' : '') . ">{$category}</option>", PHP_EOL;
                } ?>
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
        <div class="col-sm-10">
            <input name="quantity" type="text" size="8" value="<?php echo (isset($post['quantity'])) ? htmlentities($post['quantity'], Settings::htmlEntityFlags()) : '1.0'; ?>">
        </div>
    </div>
    <div class="mb-3 row">
        <label for="fromUnit" class="col-sm-2 col-form-label">From Unit</label>
        <div class="col-sm-10">
            <select name="fromUnit" class="form-select">
                <?php foreach ($units[$defaultCategory] as $fromUnitCode => $fromUnitName) {
                    echo "<option value=\"{$fromUnitCode}\" " . ((isset($post['fromUnit']) && $post['fromUnit'] === $fromUnitCode) ? 'selected' : '') . ">{$fromUnitName}</option>", PHP_EOL;
                } ?>
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <label for="toUnit" class="col-sm-2 col-form-label">To Unit</label>
        <div class="col-sm-10">
            <select name="toUnit" class="form-select">
                <?php foreach ($units[$defaultCategory] as $toUnitCode => $toUnitName) {
                    echo "<option value=\"{$toUnitCode}\" " . ((isset($post['toUnit']) && $post['toUnit'] === $toUnitCode) ? 'selected' : '') . ">{$toUnitName}</option>", PHP_EOL;
                } ?>
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-sm-10">
            <input  class="btn btn-primary" name="submitx" type="submit" value="Convert"><br />
        </div>
    </div>
</form>

<?php
/**     If the user has submitted the form, then we need to calculate the value and display the result */
if (isset($post['quantity'], $post['fromUnit'], $post['toUnit'])) {
    $quantity = $post['quantity'];
    $fromUnit = $post['fromUnit'];
    $toUnit = $post['toUnit'];
    if (!is_numeric($quantity)) {
        $helper->log('Quantity is not numeric');
    } elseif (isset($units[$post['category']][$fromUnit], $units[$post['category']][$toUnit])) {
        /** @var float|string */
        $result = ConvertUOM::CONVERT($quantity, $fromUnit, $toUnit);

        $helper->log("{$quantity} {$units[$post['category']][$fromUnit]} is {$result} {$units[$post['category']][$toUnit]}");
    } else {
        $helper->log('Please enter quantity and select From Unit and To Unit');
    }
} else {
    $helper->log('Please enter quantity and select From Unit and To Unit');
}
