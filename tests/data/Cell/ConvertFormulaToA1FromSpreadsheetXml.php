<?php

return [
    ['=B4+C4', 'of:=[.B4]+[.C4]'],
    ['=SUM(B1:B4)', 'of:=SUM([.B1:.B4])'],
    ['=CONCAT("Result of formula expression =[.B4]+[.C4] is: ", B4+C4)', 'of:=CONCAT("Result of formula expression =[.B4]+[.C4] is: ", [.B4]+[.C4])'],
];
