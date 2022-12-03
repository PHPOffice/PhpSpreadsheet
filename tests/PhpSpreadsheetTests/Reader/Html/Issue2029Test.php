<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class Issue2029Test extends TestCase
{
    public function testIssue2029(): void
    {
        $content = <<<'EOF'
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>Declaracion en Linea</title>
            </head>
            <body>
              <table>
                <tr>
                  <td>
                    <table>
                        <tr>
                          <td>
                            <table>
                            <tbody>
                            <tr>
                              <td>CUIT:</td>
                              <td><label id="lblCUIT" class="text-left">30-53914190-9</label></td>
                            </tr>
                            <tr>
                              <td>Per&iacute;odo</td>
                              <td><label id="lblPeriodo" class="text-left">02 2021</label></td>
                            </tr>
                            <tr>
                              <td>Secuencia:</td>
                              <td><label id="lblSecuencia" class="text-left">0 - Original</label></td>
                            </tr>
                            <tr>
                              <td>Contribuyente:</td>
                              <td><label id="lblContribuyente">SIND DE TRABAJADORES DE IND DE LA ALIMENTACION</label></td>
                              <td><label id="lblFechaHoy"></label></td>
                            </tr>
                            </tbody>
                            </table>
                          </td>
                        </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <table border="1px">
                <tr>
                  <th class="text-center">
                      CUIL
                  </th>
                  <th class="text-center">
                      Apellido y Nombre
                  </th>                        
                  <th class="text-center">
                      Obra Social
                  </th>
                  <th class="text-center">
                    Corresponde Reducci&oacute;n?
                  </th>                        
                </tr>
                
                <tr>
                    <td class="text-center">
                      12345678901
                    </td>
                    <td class="text-center">
                      EMILIANO ZAPATA SALAZAR
                    </td>                        
                    <td class="text-center">
                      101208
                    </td>
                    <td class="text-center">
                      Yes
                    </td>                        
                </tr>
                
                <tr>
                    <td class="text-center">
                      23456789012
                    </td>
                    <td class="text-center">
                      FRANCISCO PANCHO VILLA
                    </td>                        
                    <td class="text-center">
                      101208
                    </td>
                    <td class="text-center">
                      No
                    </td>                        
                </tr>
              </table>
            </body>
            </html>

            EOF;
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('CUIT:', $sheet->getCell('A1')->getValue());
        self::assertSame('30-53914190-9', $sheet->getCell('B1')->getValue());
        self::assertSame('Contribuyente:', $sheet->getCell('A4')->getValue());
        self::assertSame('Apellido y Nombre', $sheet->getCell('B9')->getValue());
        self::assertEquals('101208', $sheet->getCell('C10')->getValue());
        self::assertEquals('Yes', $sheet->getCell('D10')->getValue());
        self::assertEquals('23456789012', $sheet->getCell('A11')->getValue());
        self::assertEquals('No', $sheet->getCell('D11')->getValue());
    }
}
