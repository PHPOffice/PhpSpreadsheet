# PhpSpreadsheet Developer Documentation

## Credits

Please refer to the internet page [http://www.codeplex.com/PHPExcel/Wiki/View.aspx?title=Credits&referringTitle=Home][22] for up-to-date credits.

## Valid array keys for style applyFromArray()

The following table lists the valid array keys for \PhpOffice\PhpSpreadsheet\Style applyFromArray() classes. If the "Maps to property" column maps a key to a setter, the value provided for that key will be applied directly. If the "Maps to property" column maps a key to a getter, the value provided for that key will be applied as another style array.

__\PhpOffice\PhpSpreadsheet\Style__

    Array key    | Maps to property
    -------------|-------------------
    fill         | getFill()
    font         | getFont()
    borders      | getBorders()
    alignment    | getAlignment()
    numberformat | getNumberFormat()
    protection   | getProtection()


__\PhpOffice\PhpSpreadsheet\Style\Fill__

    Array key  | Maps to property
    -----------|-------------------
    type       | setFillType()
    rotation   | setRotation()
    startcolor | getStartColor()
    endcolor   | getEndColor()
    color      | getStartColor()


__\PhpOffice\PhpSpreadsheet\Style\Font__

    Array key   | Maps to property
    ------------|-------------------
    name        | setName()
    bold        | setBold()
    italic      | setItalic()
    underline   | setUnderline()
    strike      | setStrikethrough()
    color       | getColor()
    size        | setSize()
    superScript | setSuperScript()
    subScript   | setSubScript()


__\PhpOffice\PhpSpreadsheet\Style\Borders__

    Array key         | Maps to property
    ------------------|-------------------
    allborders        | getLeft(); getRight(); getTop(); getBottom()
    left              | getLeft()
    right             | getRight()
    top               | getTop()
    bottom            | getBottom()
    diagonal          | getDiagonal()
    vertical          | getVertical()
    horizontal        | getHorizontal()
    diagonaldirection | setDiagonalDirection()
    outline           | setOutline()


__\PhpOffice\PhpSpreadsheet\Style\Border__

    Array key | Maps to property
    ----------|-------------------
    style     | setBorderStyle()
    color     | getColor()


__\PhpOffice\PhpSpreadsheet\Style\Alignment__

    Array key   | Maps to property
    ------------|-------------------
    horizontal  | setHorizontal()
    vertical    | setVertical()
    rotation    | setTextRotation()
    wrap        | setWrapText()
    shrinkToFit | setShrinkToFit()
    indent      | setIndent()


__\PhpOffice\PhpSpreadsheet\Style\NumberFormat__

    Array key | Maps to property
    ----------|-------------------
    code      | setFormatCode()


__\PhpOffice\PhpSpreadsheet\Style\Protection__

    Array key | Maps to property
    ----------|-------------------
    locked    | setLocked()
    hidden    | setHidden()


  [22]: http://www.codeplex.com/PHPExcel/Wiki/View.aspx?title=Credits&referringTitle=Home
