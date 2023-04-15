<?php

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

//  value, format, result

return [
    [
        '0.0',
        0.0,
        '0.0',
    ],
    [
        '0',
        0.0,
        '0',
    ],
    [
        '0.0',
        0,
        '0.0',
    ],
    [
        '0',
        0,
        '0',
    ],
    [
        '000',
        0,
        '##0',
    ],
    [
        '12.00',
        12,
        '#.0#',
    ],
    [
        '0.1',
        0.10000000000000001,
        '0.0',
    ],
    [
        '0',
        0.10000000000000001,
        '0',
    ],
    [
        '5.556',
        5.5555000000000003,
        '0.###',
    ],
    [
        '5.556',
        5.5555000000000003,
        '0.0##',
    ],
    [
        '5.556',
        5.5555000000000003,
        '0.00#',
    ],
    [
        '12 345.67',
        12345.67,
        '#\ ##0.00',
    ],
    [
        '1234 567.00',
        1234567.00,
        '#\ ##0.00',
    ],
    [
        '5.556',
        5.5555000000000003,
        '0.000',
    ],
    [
        '5.5555',
        5.5555000000000003,
        '0.0000',
    ],
    [
        '12,345.68',
        12345.678900000001,
        '#,##0.00',
    ],
    [
        '12,345.679',
        12345.678900000001,
        '#,##0.000',
    ],
    [
        '12.34 kg',
        12.34,
        '0.00 "kg"',
    ],
    [
        'kg 12.34',
        12.34,
        '"kg" 0.00',
    ],
    [
        '12.34 kg.',
        12.34,
        '0.00 "kg."',
    ],
    [
        'kg. 12.34',
        12.34,
        '"kg." 0.00',
    ],
    [
        '£ 12,345.68',
        12345.678900000001,
        '£ #,##0.00',
    ],
    [
        '$ 12,345.679',
        12345.678900000001,
        '$ #,##0.000',
    ],
    [
        '12,345.679 €',
        12345.678900000001,
        '#,##0.000\ [$€-1]',
    ],
    [
        '12,345.679 $',
        12345.678900000001,
        '#,##0.000\ [$]',
    ],
    'Spacing Character' => [
        '826.00  €',
        826,
        '#,##0.00 __€',
    ],
    [
        '5.68',
        5.6788999999999996,
        '#,##0.00',
    ],
    [
        '12,000',
        12000,
        '#,###',
    ],
    [
        '12',
        12000,
        '#,',
    ],
    // Scaling test
    [
        '12.2',
        12200000,
        '0.0,,',
    ],
    // Percentage
    [
        '12%',
        0.12,
        '0%',
    ],
    [
        '8%',
        0.080000000000000002,
        '0%',
    ],
    [
        '80%',
        0.80000000000000004,
        '0%',
    ],
    [
        '280%',
        2.7999999999999998,
        '0%',
    ],
    [
        '$125.74 Surplus',
        125.73999999999999,
        '$0.00" Surplus";$-0.00" Shortage"',
    ],
    [
        '$-125.74 Shortage',
        -125.73999999999999,
        '$0.00" Surplus";$-0.00" Shortage"',
    ],
    [
        '$125.74 Shortage',
        -125.73999999999999,
        '$0.00" Surplus";$0.00" Shortage"',
    ],
    [
        '12%',
        0.123,
        '0%',
    ],
    [
        '10%',
        0.1,
        '0%',
    ],
    [
        '10.0%',
        0.1,
        '0.0%',
    ],
    [
        '-12%',
        -0.123,
        '0%',
    ],
    [
        '12.3  %',
        0.123,
        '0.?? %',
    ],
    [
        '12.35 %',
        0.12345,
        '0.?? %',
    ],
    [
        '12.345  %',
        0.12345,
        '0.00?? %',
    ],
    [
        '12.3457 %',
        0.123456789,
        '0.00?? %',
    ],
    [
        '-12.3  %',
        -0.123,
        '0.?? %',
    ],
    [
        '12.30 %age',
        0.123,
        '0.00 %"age"',
    ],
    [
        '-12.30 %age',
        -0.123,
        '0.00 %"age"',
    ],
    [
        '12.30%',
        0.123,
        '0.00%;(0.00%)',
    ],
    [
        '(12.30%)',
        -0.123,
        '0.00%;(0.00%)',
    ],
    [
        '12.30% ',
        0.123,
        '0.00%_;( 0.00% )',
    ],
    [
        '( 12.30% )',
        -0.123,
        '_(0.00%_;( 0.00% )',
    ],
    // Complex formats
    [
        '(001) 2-3456-789',
        123456789,
        '(000) 0-0000-000',
    ],
    [
        '0 (+00) 0123 45 67 89',
        123456789,
        '0 (+00) 0000 00 00 00',
    ],
    [
        '002-01-0035-7',
        20100357,
        '000-00-0000-0',
    ],
    [
        '002-01-00.35-7',
        20100.357,
        '000-00-00.00-0',
    ],
    [
        '002.01.0035.7',
        20100357,
        '000\.00\.0000\.0',
    ],
    [
        '002.01.00.35.7',
        20100.357,
        '000\.00\.00.00\.0',
    ],
    [
        '002.01.00.35.70',
        20100.357,
        '000\.00\.00.00\.00',
    ],
    [
        '12345:67:89',
        123456789,
        '0000:00:00',
    ],
    [
        '-12345:67:89',
        -123456789,
        '0000:00:00',
    ],
    [
        '12345:67.89',
        1234567.8899999999,
        '0000:00.00',
    ],
    [
        '-12345:67.89',
        -1234567.8899999999,
        '0000:00.00',
    ],
    [
        '18.952',
        18.952,
        '[$-409]General',
    ],
    [
        '9.98',
        9.98,
        '[$-409]#,##0.00;-#,##0.00',
    ],
    [
        '18.952',
        18.952,
        '[$-1010409]General',
    ],
    [
        '9.98',
        9.98,
        '[$-1010409]#,##0.00;-#,##0.00',
    ],
    [
        ' $ 23.06 ',
        23.0597,
        '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
    ],
    [
        ' € (13.03)',
        -13.0316,
        '_("€"* #,##0.00_);_("€"* \(#,##0.00\);_("€"* "-"??_);_(@_)',
    ],
    [
        ' € 11.70 ',
        11.7,
        '_-€* #,##0.00_-;"-€"* #,##0.00_-;_-€* -??_-;_-@_-',
    ],
    [
        '-€ 12.14 ',
        -12.14,
        '_-€* #,##0.00_-;"-€"* #,##0.00_-;_-€* -??_-;_-@_-',
    ],
    [
        ' € -   ',
        0,
        '_-€* #,##0.00_-;"-€"* #,##0.00_-;_-€* -??_-;_-@_-',
    ],
    [
        'test',
        'test',
        '_-€* #,##0.00_-;"-€"* #,##0.00_-;_-€* -??_-;_-@_-',
    ],
    // String masks (ie. @)
    [
        'World',
        'World',
        '@',
    ],
    [
        'Hello World',
        'World',
        'Hello @',
    ],
    [
        'Hello World',
        'World',
        '"Hello "@',
    ],
    [
        'Meet me @ The Boathouse @ 16:30',
        'The Boathouse',
        '"Meet me @ "@" @ 16:30"',
    ],
    // Named colours
    // Simple color
    [
        '12345',
        12345,
        '[Green]General',
    ],
    [
        '12345',
        12345,
        '[GrEeN]General',
    ],
    [
        '-70',
        -70,
        '#,##0;[Red]-#,##0',
    ],
    [
        '-12,345',
        -12345,
        '#,##0;[Red]-#,##0',
    ],
    // Multiple colors
    [
        '12345',
        12345,
        '[Blue]0;[Red]0-',
    ],
    [
        '12345-',
        -12345,
        '[BLUE]0;[red]0-',
    ],
    [
        '12345-',
        -12345,
        '[blue]0;[RED]0-',
    ],
    // Multiple colors with text substitution
    [
        'Positive',
        12,
        '[Green]"Positive";[Red]"Negative";[Blue]"Zero"',
    ],
    [
        'Zero',
        0,
        '[Green]"Positive";[Red]"Negative";[Blue]"Zero"',
    ],
    [
        'Negative',
        -2,
        '[Green]"Positive";[Red]"Negative";[Blue]"Zero"',
    ],
    // Colour palette index
    [
        '+710',
        710,
        '[color 10]+#,##0;[color 12]-#,##0',
    ],
    [
        '-710',
        -710,
        '[color 10]+#,##0;[color 12]-#,##0',
    ],
    // Colour palette index
    [
        '+710',
        710,
        '[color10]+#,##0;[color12]-#,##0',
    ],
    [
        '-710',
        -710,
        '[color10]+#,##0;[color12]-#,##0',
    ],
    [
        '-710',
        -710,
        '[color01]+#,##0;[color02]-#,##0',
    ],
    [
        '-710',
        -710,
        '[color08]+#,##0;[color09]-#,##0',
    ],
    [
        '-710',
        -710,
        '[color55]+#,##0;[color56]-#,##0',
    ],
    // Value break points
    [
        '<=3500 red',
        3500,
        '[Green][=17]"=17 green";[Red][<=3500]"<=3500 red";[Blue]"Zero"',
    ],
    [
        '=17 green',
        17,
        '[Green][=17]"=17 green";[Red][<=3500]"<=3500 red";[Blue]"Zero"',
    ],
    [
        '<>25 green',
        17,
        '[Green][<>25]"<>25 green";[Red]"else red"',
    ],
    [
        'else red',
        25,
        '[Green][<>25]"<>25 green";[Red]"else red"',
    ],
    // Leading/trailing quotes in mask
    [
        '$12.34 ',
        12.34,
        '$#,##0.00_;[RED]"($"#,##0.00")"',
    ],
    [
        '($12.34)',
        -12.34,
        '$#,##0.00_;[RED]"($"#,##0.00")"',
    ],
    [
        'pfx. 25.00',
        25,
        '"pfx." 0.00;"pfx." -0.00;"pfx." 0.00;',
    ],
    [
        'pfx. 25.20',
        25.2,
        '"pfx." 0.00;"pfx." -0.00;"pfx." 0.00;',
    ],
    [
        'pfx. -25.20',
        -25.2,
        '"pfx." 0.00;"pfx." -0.00;"pfx." 0.00;',
    ],
    [
        'pfx. 25.26',
        25.255555555555555,
        '"pfx." 0.00;"pfx." -0.00;"pfx." 0.00;',
    ],
    [
        '1',
        '1.000',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '-1',
        '-1.000',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '1',
        '1',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '-1',
        '-1',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '0',
        '0',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '0',
        '-0',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '1',
        '1.1',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '1',
        '1.4',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '2',
        '1.5',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '2',
        '1.9',
        NumberFormat::FORMAT_NUMBER,
    ],
    [
        '1.0',
        '1.000',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '-1.0',
        '-1.000',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '1.0',
        '1',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '-1.0',
        '-1',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '1.0',
        '1',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '0.0',
        '0',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '0.0',
        '-0',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '1.1',
        '1.11',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '1.1',
        '1.14',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '1.2',
        '1.15',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '1.2',
        '1.19',
        NumberFormat::FORMAT_NUMBER_0,
    ],
    [
        '0.00',
        '0',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '1.00',
        '1',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '1.11',
        '1.111',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '1.11',
        '1.114',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '1.12',
        '1.115',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '1.12',
        '1.119',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '0.00',
        '-0',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '-1.00',
        '-1',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '-1.11',
        '-1.111',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '-1.11',
        '-1.114',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '-1.12',
        '-1.115',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '-1.12',
        '-1.119',
        NumberFormat::FORMAT_NUMBER_00,
    ],
    [
        '0.00',
        '0',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '1,000.00',
        '1000',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '1,111.11',
        '1111.111',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '1,111.11',
        '1111.114',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '1,111.12',
        '1111.115',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '1,111.12',
        '1111.119',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '0.00',
        '-0',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '-1,111.00',
        '-1111',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '-1,111.11',
        '-1111.111',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '-1,111.11',
        '-1111.114',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '-1,111.12',
        '-1111.115',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '-1,111.12',
        '-1111.119',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
    ],
    [
        '0.00 ',
        '0',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '1,000.00 ',
        '1000',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '1,111.11 ',
        '1111.111',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '1,111.11 ',
        '1111.114',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '1,111.12 ',
        '1111.115',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '1,111.12 ',
        '1111.119',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '0.00 ',
        '-0',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '-1,111.00 ',
        '-1111',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '-1,111.11 ',
        '-1111.111',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '-1,111.11 ',
        '-1111.114',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '-1,111.12 ',
        '-1111.115',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '-1,111.12 ',
        '-1111.119',
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
    ],
    [
        '0%',
        '0',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '1%',
        '0.01',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '1%',
        '0.011',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '1%',
        '0.014',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '2%',
        '0.015',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '2%',
        '0.019',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '0%',
        '-0',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '-1%',
        '-0.01',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '-1%',
        '-0.011',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '-1%',
        '-0.014',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '-2%',
        '-0.015',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '-2%',
        '-0.019',
        NumberFormat::FORMAT_PERCENTAGE,
    ],
    [
        '0.0%',
        '0',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '1.0%',
        '0.01',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '1.1%',
        '0.011',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '1.1%',
        '0.0114',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '1.2%',
        '0.0115',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '1.2%',
        '0.0119',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '0.0%',
        '-0',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '-1.0%',
        '-0.01',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '-1.1%',
        '-0.011',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '-1.1%',
        '-0.0114',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '-1.2%',
        '-0.0115',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '-1.2%',
        '-0.0119',
        NumberFormat::FORMAT_PERCENTAGE_0,
    ],
    [
        '0.00%',
        '0',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '1.00%',
        '0.01',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '1.11%',
        '0.0111',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '1.11%',
        '0.01114',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '1.12%',
        '0.01115',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '1.12%',
        '0.01119',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '0.00%',
        '-0',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '-1.00%',
        '-0.01',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '-1.11%',
        '-0.0111',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '-1.11%',
        '-0.01114',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '-1.12%',
        '-0.01115',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '-1.12%',
        '-0.01119',
        NumberFormat::FORMAT_PERCENTAGE_00,
    ],
    [
        '$0.00 ',
        '0',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,000.00 ',
        '1000',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.11 ',
        '1111.111',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.11 ',
        '1111.114',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.12 ',
        '1111.115',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.12 ',
        '1111.119',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$0.00 ',
        '-0',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.00 ',
        '-1111',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.11 ',
        '-1111.111',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.11 ',
        '-1111.114',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.12 ',
        '-1111.115',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.12 ',
        '-1111.119',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$0 ',
        '0',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,000 ',
        '1000',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,111 ',
        '1111.1',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,111 ',
        '1111.4',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,112 ',
        '1111.5',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,112 ',
        '1111.9',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$0 ',
        '-0',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,111 ',
        '-1111',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,111 ',
        '-1111.1',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,111 ',
        '-1111.4',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,112 ',
        '-1111.5',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,112 ',
        '-1111.9',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$0.00 ',
        '0',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,000.00 ',
        '1000',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.11 ',
        '1111.111',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.11 ',
        '1111.114',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.12 ',
        '1111.115',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$1,111.12 ',
        '1111.119',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$0.00 ',
        '-0',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.00 ',
        '-1111',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.11 ',
        '-1111.111',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.11 ',
        '-1111.114',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.12 ',
        '-1111.115',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$-1,111.12 ',
        '-1111.119',
        NumberFormat::FORMAT_CURRENCY_USD,
    ],
    [
        '$0 ',
        '0',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,000 ',
        '1000',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,111 ',
        '1111.1',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,111 ',
        '1111.4',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,112 ',
        '1111.5',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$1,112 ',
        '1111.9',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$0 ',
        '-0',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,111 ',
        '-1111',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,111 ',
        '-1111.1',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,111 ',
        '-1111.4',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,112 ',
        '-1111.5',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '$-1,112 ',
        '-1111.9',
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER,
    ],
    [
        '0.00 €',
        '0',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '1,000.00 €',
        '1000',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '1,111.11 €',
        '1111.111',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '1,111.11 €',
        '1111.114',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '1,111.12 €',
        '1111.115',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '1,111.12 €',
        '1111.119',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '0.00 €',
        '-0',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '-1,111.00 €',
        '-1111',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '-1,111.11 €',
        '-1111.111',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '-1,111.11 €',
        '-1111.114',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '-1,111.12 €',
        '-1111.115',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '-1,111.12 €',
        '-1111.119',
        NumberFormat::FORMAT_CURRENCY_EUR,
    ],
    [
        '0 €',
        '0',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '1,000 €',
        '1000',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '1,111 €',
        '1111.1',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '1,111 €',
        '1111.4',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '1,112 €',
        '1111.5',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '1,112 €',
        '1111.9',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '0 €',
        '-0',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '-1,111 €',
        '-1111',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '-1,111 €',
        '-1111.1',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '-1,111 €',
        '-1111.4',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '-1,112 €',
        '-1111.5',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        '-1,112 €',
        '-1111.9',
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER,
    ],
    [
        ' $ -   ',
        '0',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ 1,000.00 ',
        '1000',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ 1,111.11 ',
        '1111.111',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ 1,111.11 ',
        '1111.114',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ 1,111.12 ',
        '1111.115',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ 1,111.12 ',
        '1111.119',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ -   ',
        '-0',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ (1,111.00)',
        '-1111',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ (1,111.11)',
        '-1111.111',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ (1,111.11)',
        '-1111.114',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ (1,111.12)',
        '-1111.115',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' $ (1,111.12)',
        '-1111.119',
        NumberFormat::FORMAT_ACCOUNTING_USD,
    ],
    [
        ' € -   ',
        '0',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € 1,000.00 ',
        '1000',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € 1,111.11 ',
        '1111.111',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € 1,111.11 ',
        '1111.114',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € 1,111.12 ',
        '1111.115',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € 1,111.12 ',
        '1111.119',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € -   ',
        '-0',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € (1,111.00)',
        '-1111',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € (1,111.11)',
        '-1111.111',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € (1,111.11)',
        '-1111.114',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € (1,111.12)',
        '-1111.115',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    [
        ' € (1,111.12)',
        '-1111.119',
        NumberFormat::FORMAT_ACCOUNTING_EUR,
    ],
    'issue 1929' => ['(79.3%)', -0.793, '#,##0.0%;(#,##0.0%)'],
    'percent without leading 0' => ['6.2%', 0.062, '##.0%'],
    'percent with leading 0' => ['06.2%', 0.062, '00.0%'],
    'percent lead0 no decimal' => ['06%', 0.062, '00%'],
    'percent nolead0 no decimal' => ['6%', 0.062, '##%'],
    'scientific small complex mask discard all decimals' => ['0 000.0', 1e-17, '0 000.0'],
    'scientific small complex mask keep some decimals' => ['-0 000.000027', -2.7e-5, '0 000.000000'],
    'scientific small complex mask keep some decimals trailing zero' => ['-0 000.000040', -4e-5, '0 000.000000'],
    'scientific large complex mask' => ['92' . str_repeat('0', 13) . ' 000.0', 9.2e17, '0 000.0'],
    'scientific very large complex mask PhpSpreadsheet does not match Excel' => ['1' . str_repeat('0', 18), 1e18, '0 000.0'],
    'scientific even larger complex mask PhpSpreadsheet does not match Excel' => ['43' . str_repeat('0', 89), 4.3e90, '0 000.0'],
    'scientific many decimal positions' => ['000 0.000 01', 1e-5, '000 0.000 00'],
    'round with scientific notation' => ['000 0.000 02', 1.6e-5, '000 0.000 00'],
    'round with no decimals' => ['009 8', 97.7, '000 0'],
    'round to 1 decimal' => ['009 7.2', 97.15, '000 0.0'],
    'truncate with no decimals' => ['009 7', 97.3, '000 0'],
    'truncate to 1 decimal' => ['009 7.1', 97.13, '000 0.0'],
    'scientific many decimal positions truncated' => ['000 0.000 00', 1e-7, '000 0.000 00'],
    'scientific very many decimal positions truncated' => ['000 0.000 00', 1e-17, '000 0.000 00'],
    [
        '€ 1,111.12 ',
        '1111.119',
        '[$€-nl_NL]_(#,##0.00_);[$€-nl_NL] (#,##0.00)',
    ],
    [
        '€ (1,111.12)',
        '-1111.119',
        '[$€-nl_NL]_(#,##0.00_);[$€-nl_NL] (#,##0.00)',
    ],
    [
        '€ 1,111.12 ',
        '1111.119',
        '[$€-en_US]_(#,##0.00_);[$€-en_US] (#,##0.00)',
    ],
    [
        '-1.2E+4',
        -12345.6789,
        '0.0E+00',
    ],
    [
        '-1.23E+4',
        -12345.6789,
        '0.00E+00',
    ],
    [
        '-1.235E+4',
        -12345.6789,
        '0.000E+00',
    ],
    [
        'Product SKU #12345',
        12345,
        '"Product SKU #"0',
    ],
    [
        'Product SKU #12-345',
        12345,
        '"Product SKU #"00-000',
    ],
    [
        '€12,345.74 Surplus for Product #12-345',
        12345.74,
        '[$€]#,##0.00" Surplus for Product #12-345";$-#,##0.00" Shortage for Product #12-345"',
    ],
    // Scaling
    [
        '12,000',
        12000,
        '#,###',
    ],
    [
        '12',
        12000,
        '#,',
    ],
    [
        '0',
        120,
        '#,',
    ],
    [
        '0.12',
        120,
        '#,.00',
    ],
    [
        '12k',
        12000,
        '#,k',
    ],
    [
        '12.2',
        12200000,
        '0.0,,',
    ],
    [
        '12.2 M',
        12200000,
        '0.0,, M',
    ],
    [
        '1,025.13',
        1025132.36,
        '#,###,.##',
    ],
    [
        '.05',
        50,
        '#.00,',
    ],
    [
        '50.05',
        50050,
        '#.00,',
    ],
    [
        '555.50',
        555500,
        '#.00,',
    ],
    [
        '.56',
        555500,
        '#.00,,',
    ],
    // decimal placement
    [
        ' 44.398',
        44.398,
        '???.???',
    ],
    [
        '102.65 ',
        102.65,
        '???.???',
    ],
    [
        '  2.8  ',
        2.8,
        '???.???',
    ],
    [
        '  3',
        2.8,
        '???',
    ],
    [
        '12,345',
        12345,
        '?,???',
    ],
    [
        '123',
        123,
        '?,???',
    ],
    [
        '$.50',
        0.5,
        '$?.00',
    ],
    [
        'Part Cost $.50',
        0.5,
        'Part Cost $?.00',
    ],
    // Empty Section
    [
        '',
        -12345.6789,
        '#,##0.00;',
    ],
    [
        '',
        -12345.6789,
        '#,##0.00;;"---"',
    ],
];
