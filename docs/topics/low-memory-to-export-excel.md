#Low memory way to export Excel

When we need to export a big Excel, it usually needs much memory to finish it.Memory saving method can 
help us use less memory to create an Excel.However, this way need external storage and cost more time.
Using "Low memory way" can make you create an Excel with very low memory, and the same time as you use `save()`
to export, or maybe better.

##How to use 
Control the data save

```
$objPHPExcel = new Spreadsheet();
$objWriter = new Writer\XlsxNew($objPHPExcel);

//before you start load data, or set other style,comment,image etc,
// call method prepareBeforeSave
$objWriter->prepareBeforeSave('/path/to/save.xlsx');

//before ever sheet you're new 
//you need to call saveSheetHeader once
$objActSheet = $writer->saveSheetHeader();

//now, you can load you data
for ($r = 0,$ii = 0; $ii < 5; ++$ii) {
    $objActSheet->setCellValue('C' . $r, $r);
    $objActSheet->setCellValue('D' . $r, $r);
    //the key to save memory is here, when you want to release memory, just call saveSheetFormData
    $objActSheet = $objWriter->saveSheetFormData();
}
//any special data need to be set is ok
$objActSheet->setCellValue('C1', 'some data');

//you can also call saveSheetFormData to release memory right now,
// but you don't need to call it if you don't want to release.
$objActSheet = $objWriter->saveSheetFormData();
...
...

//when you make sure a sheet data is no more data to load, call afterSaveSheetFormData once
//remember the method :saveSheetHeader(), afterSaveSheetFormData() can only be called once of each sheet
$objWriter->afterSaveSheetFormData();

//when you need to finish, and need to export Excel, call finish()
$objWriter->finish();
```

Create many sheets
--
Easy, just new a sheet after method `afterSaveSheetFormData()` like this:
```
$objWriter->afterSaveSheetFormData();

$objPHPExcel->createSheet(1);
$objPHPExcel->setActiveSheetIndex(1);
//repeat call saveSheetHeader() after new a sheet
$objActSheet = $writer->saveSheetHeader();

//开始表单循环
for ($r = 0,$ii = 0; $ii < 5; ++$ii) {
    $objActSheet->setCellValue('C' . $r, $r);
    $objActSheet->setCellValue('D' . $r, $r);
    //call saveSheetFormData() same
    $objActSheet = $objWriter->saveSheetFormData();
}
//here is also the same
$objWriter->afterSaveSheetFormData();

$objWriter->finish();
```
Yes, what we do is to repeat calling `saveSheetHeader()`,`saveSheetFormData()`, `afterSaveSheetFormData`.
Compare with `save()`, it seems a little complex, but it is still easy to export.

If you want to save sheets data in turn is ok:
```
//save sheet 0 first
$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setCellValue('E4', 1);

//save sheet 1 second
$objPHPExcel->setActiveSheetIndex(1);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setCellValue('E4', 1);

$objWriter->afterSaveSheetFormData();//save sheet 1

//write sheet 0 again
$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setCellValue('E5', 1);
$objWriter->afterSaveSheetFormData();//save sheet 0
```   
If you don't call `afterSaveSheetFormData()`, sheet won't be closed and you can `setActiveSheetIndex` to keep write.


##What needs to be focus
When you set a cell's value, it must be in order of row.
```
$objActSheet->setCellValue('E4', 1);
$objActSheet->setCellValue('E5', 10);
$objActSheet = $objWriter->saveSheetFormData();

//after you call saveSheetFormData(), and you set a cell row number is less than
//the largest one you have ever set, it will be useless,
$objActSheet->setCellValue('E4', 10);//E4 is still 1
$objActSheet->setCellValue('A4', 10);//do not work!
```

You must call `saveSheetHeader()` and `afterSaveSheetFormData()` in pair, or the format is fault.

If you want a better export performance, such as low memory allocated, you need to call `saveSheetFormData()`
before you set many cells.<br>
Meanwhile, you shouldn't call `saveSheetFormData()` frequently when you just set several cells because it will cost more time.<br>
Luckily, it just cost several seconds more.  

##Performance comparison
hardware :<br>
MacBook Pro (16-inch, 2019)<br>
2.6GHz 6-core Intel Core i7 processor<br> 
16 GB 2667 MHz DDR4

create an Excel with 50000 rows of sheet 0, 100 rows of sheet 1.
1. old way to use `save()`, the amount of memory allocated : 191369216 bytes, time cost : 54 seconds
1. new way of this low memory, the amount of memory allocated : 10485760 bytes, time cost : 47.473015069962 seconds