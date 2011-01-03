<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_Excel2007_DocProps
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Theme extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write theme to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeTheme(PHPExcel $pPHPExcel = null)
	{
			// Create XML writer
			$objWriter = null;
			if ($this->getParentWriter()->getUseDiskCaching()) {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
			} else {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
			}

			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');

			// a:theme
			$objWriter->startElement('a:theme');
			$objWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
			$objWriter->writeAttribute('name', 'Office Theme');

				// a:themeElements
				$objWriter->startElement('a:themeElements');

					{
					// a:clrScheme
					$objWriter->startElement('a:clrScheme');
					$objWriter->writeAttribute('name', 'Office');

						// a:dk1
						$objWriter->startElement('a:dk1');

							// a:sysClr
							$objWriter->startElement('a:sysClr');
							$objWriter->writeAttribute('val', 'windowText');
							$objWriter->writeAttribute('lastClr', '000000');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:lt1
						$objWriter->startElement('a:lt1');

							// a:sysClr
							$objWriter->startElement('a:sysClr');
							$objWriter->writeAttribute('val', 'window');
							$objWriter->writeAttribute('lastClr', 'FFFFFF');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:dk2
						$objWriter->startElement('a:dk2');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', '1F497D');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:lt2
						$objWriter->startElement('a:lt2');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', 'EEECE1');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:accent1
						$objWriter->startElement('a:accent1');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', '4F81BD');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:accent2
						$objWriter->startElement('a:accent2');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', 'C0504D');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:accent3
						$objWriter->startElement('a:accent3');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', '9BBB59');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:accent4
						$objWriter->startElement('a:accent4');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', '8064A2');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:accent5
						$objWriter->startElement('a:accent5');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', '4BACC6');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:accent6
						$objWriter->startElement('a:accent6');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', 'F79646');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:hlink
						$objWriter->startElement('a:hlink');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', '0000FF');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:folHlink
						$objWriter->startElement('a:folHlink');

							// a:sysClr
							$objWriter->startElement('a:srgbClr');
							$objWriter->writeAttribute('val', '800080');
							$objWriter->endElement();

						$objWriter->endElement();

					$objWriter->endElement();
					}

					{
					// a:fontScheme
					$objWriter->startElement('a:fontScheme');
					$objWriter->writeAttribute('name', 'Office');

						// a:majorFont
						$objWriter->startElement('a:majorFont');

							// a:latin
							$objWriter->startElement('a:latin');
							$objWriter->writeAttribute('typeface', 'Cambria');
							$objWriter->endElement();

							// a:ea
							$objWriter->startElement('a:ea');
							$objWriter->writeAttribute('typeface', '');
							$objWriter->endElement();

							// a:cs
							$objWriter->startElement('a:cs');
							$objWriter->writeAttribute('typeface', '');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Jpan');
							$objWriter->writeAttribute('typeface', '?? ?????');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hang');
							$objWriter->writeAttribute('typeface', '?? ??');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hans');
							$objWriter->writeAttribute('typeface', '??');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hant');
							$objWriter->writeAttribute('typeface', '????');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Arab');
							$objWriter->writeAttribute('typeface', 'Times New Roman');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hebr');
							$objWriter->writeAttribute('typeface', 'Times New Roman');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Thai');
							$objWriter->writeAttribute('typeface', 'Tahoma');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Ethi');
							$objWriter->writeAttribute('typeface', 'Nyala');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Beng');
							$objWriter->writeAttribute('typeface', 'Vrinda');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Gujr');
							$objWriter->writeAttribute('typeface', 'Shruti');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Khmr');
							$objWriter->writeAttribute('typeface', 'MoolBoran');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Knda');
							$objWriter->writeAttribute('typeface', 'Tunga');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Guru');
							$objWriter->writeAttribute('typeface', 'Raavi');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Cans');
							$objWriter->writeAttribute('typeface', 'Euphemia');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Cher');
							$objWriter->writeAttribute('typeface', 'Plantagenet Cherokee');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Yiii');
							$objWriter->writeAttribute('typeface', 'Microsoft Yi Baiti');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Tibt');
							$objWriter->writeAttribute('typeface', 'Microsoft Himalaya');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Thaa');
							$objWriter->writeAttribute('typeface', 'MV Boli');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Deva');
							$objWriter->writeAttribute('typeface', 'Mangal');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Telu');
							$objWriter->writeAttribute('typeface', 'Gautami');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Taml');
							$objWriter->writeAttribute('typeface', 'Latha');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Syrc');
							$objWriter->writeAttribute('typeface', 'Estrangelo Edessa');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Orya');
							$objWriter->writeAttribute('typeface', 'Kalinga');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Mlym');
							$objWriter->writeAttribute('typeface', 'Kartika');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Laoo');
							$objWriter->writeAttribute('typeface', 'DokChampa');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Sinh');
							$objWriter->writeAttribute('typeface', 'Iskoola Pota');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Mong');
							$objWriter->writeAttribute('typeface', 'Mongolian Baiti');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Viet');
							$objWriter->writeAttribute('typeface', 'Times New Roman');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Uigh');
							$objWriter->writeAttribute('typeface', 'Microsoft Uighur');
							$objWriter->endElement();

						$objWriter->endElement();

						// a:minorFont
						$objWriter->startElement('a:minorFont');

							// a:latin
							$objWriter->startElement('a:latin');
							$objWriter->writeAttribute('typeface', 'Calibri');
							$objWriter->endElement();

							// a:ea
							$objWriter->startElement('a:ea');
							$objWriter->writeAttribute('typeface', '');
							$objWriter->endElement();

							// a:cs
							$objWriter->startElement('a:cs');
							$objWriter->writeAttribute('typeface', '');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Jpan');
							$objWriter->writeAttribute('typeface', '?? ?????');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hang');
							$objWriter->writeAttribute('typeface', '?? ??');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hans');
							$objWriter->writeAttribute('typeface', '??');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hant');
							$objWriter->writeAttribute('typeface', '????');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Arab');
							$objWriter->writeAttribute('typeface', 'Arial');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Hebr');
							$objWriter->writeAttribute('typeface', 'Arial');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Thai');
							$objWriter->writeAttribute('typeface', 'Tahoma');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Ethi');
							$objWriter->writeAttribute('typeface', 'Nyala');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Beng');
							$objWriter->writeAttribute('typeface', 'Vrinda');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Gujr');
							$objWriter->writeAttribute('typeface', 'Shruti');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Khmr');
							$objWriter->writeAttribute('typeface', 'DaunPenh');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Knda');
							$objWriter->writeAttribute('typeface', 'Tunga');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Guru');
							$objWriter->writeAttribute('typeface', 'Raavi');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Cans');
							$objWriter->writeAttribute('typeface', 'Euphemia');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Cher');
							$objWriter->writeAttribute('typeface', 'Plantagenet Cherokee');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Yiii');
							$objWriter->writeAttribute('typeface', 'Microsoft Yi Baiti');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Tibt');
							$objWriter->writeAttribute('typeface', 'Microsoft Himalaya');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Thaa');
							$objWriter->writeAttribute('typeface', 'MV Boli');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Deva');
							$objWriter->writeAttribute('typeface', 'Mangal');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Telu');
							$objWriter->writeAttribute('typeface', 'Gautami');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Taml');
							$objWriter->writeAttribute('typeface', 'Latha');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Syrc');
							$objWriter->writeAttribute('typeface', 'Estrangelo Edessa');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Orya');
							$objWriter->writeAttribute('typeface', 'Kalinga');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Mlym');
							$objWriter->writeAttribute('typeface', 'Kartika');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Laoo');
							$objWriter->writeAttribute('typeface', 'DokChampa');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Sinh');
							$objWriter->writeAttribute('typeface', 'Iskoola Pota');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Mong');
							$objWriter->writeAttribute('typeface', 'Mongolian Baiti');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Viet');
							$objWriter->writeAttribute('typeface', 'Arial');
							$objWriter->endElement();

							// a:font
							$objWriter->startElement('a:font');
							$objWriter->writeAttribute('script', 'Uigh');
							$objWriter->writeAttribute('typeface', 'Microsoft Uighur');
							$objWriter->endElement();

						$objWriter->endElement();

					$objWriter->endElement();
					}

					{
					// a:fmtScheme
					$objWriter->startElement('a:fmtScheme');
					$objWriter->writeAttribute('name', 'Office');

						// a:fillStyleLst
						$objWriter->startElement('a:fillStyleLst');

							// a:solidFill
							$objWriter->startElement('a:solidFill');

								// a:schemeClr
								$objWriter->startElement('a:schemeClr');
								$objWriter->writeAttribute('val', 'phClr');
								$objWriter->endElement();

							$objWriter->endElement();

							// a:gradFill
							$objWriter->startElement('a:gradFill');
							$objWriter->writeAttribute('rotWithShape', '1');

								// a:gsLst
								$objWriter->startElement('a:gsLst');

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '0');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:tint
											$objWriter->startElement('a:tint');
											$objWriter->writeAttribute('val', '50000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '300000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '35000');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:tint
											$objWriter->startElement('a:tint');
											$objWriter->writeAttribute('val', '37000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '300000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '100000');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:tint
											$objWriter->startElement('a:tint');
											$objWriter->writeAttribute('val', '15000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '350000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

								// a:lin
								$objWriter->startElement('a:lin');
								$objWriter->writeAttribute('ang', '16200000');
								$objWriter->writeAttribute('scaled', '1');
								$objWriter->endElement();

							$objWriter->endElement();

							// a:gradFill
							$objWriter->startElement('a:gradFill');
							$objWriter->writeAttribute('rotWithShape', '1');

								// a:gsLst
								$objWriter->startElement('a:gsLst');

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '0');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:shade
											$objWriter->startElement('a:shade');
											$objWriter->writeAttribute('val', '51000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '130000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '80000');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:shade
											$objWriter->startElement('a:shade');
											$objWriter->writeAttribute('val', '93000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '130000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '100000');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:shade
											$objWriter->startElement('a:shade');
											$objWriter->writeAttribute('val', '94000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '135000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

								// a:lin
								$objWriter->startElement('a:lin');
								$objWriter->writeAttribute('ang', '16200000');
								$objWriter->writeAttribute('scaled', '0');
								$objWriter->endElement();

							$objWriter->endElement();

						$objWriter->endElement();

						// a:lnStyleLst
						$objWriter->startElement('a:lnStyleLst');

							// a:ln
							$objWriter->startElement('a:ln');
							$objWriter->writeAttribute('w', '9525');
							$objWriter->writeAttribute('cap', 'flat');
							$objWriter->writeAttribute('cmpd', 'sng');
							$objWriter->writeAttribute('algn', 'ctr');

								// a:solidFill
								$objWriter->startElement('a:solidFill');

									// a:schemeClr
									$objWriter->startElement('a:schemeClr');
									$objWriter->writeAttribute('val', 'phClr');

											// a:shade
											$objWriter->startElement('a:shade');
											$objWriter->writeAttribute('val', '95000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '105000');
											$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

								// a:prstDash
								$objWriter->startElement('a:prstDash');
								$objWriter->writeAttribute('val', 'solid');
								$objWriter->endElement();

							$objWriter->endElement();

							// a:ln
							$objWriter->startElement('a:ln');
							$objWriter->writeAttribute('w', '25400');
							$objWriter->writeAttribute('cap', 'flat');
							$objWriter->writeAttribute('cmpd', 'sng');
							$objWriter->writeAttribute('algn', 'ctr');

								// a:solidFill
								$objWriter->startElement('a:solidFill');

									// a:schemeClr
									$objWriter->startElement('a:schemeClr');
									$objWriter->writeAttribute('val', 'phClr');
									$objWriter->endElement();

								$objWriter->endElement();

								// a:prstDash
								$objWriter->startElement('a:prstDash');
								$objWriter->writeAttribute('val', 'solid');
								$objWriter->endElement();

							$objWriter->endElement();

							// a:ln
							$objWriter->startElement('a:ln');
							$objWriter->writeAttribute('w', '38100');
							$objWriter->writeAttribute('cap', 'flat');
							$objWriter->writeAttribute('cmpd', 'sng');
							$objWriter->writeAttribute('algn', 'ctr');

								// a:solidFill
								$objWriter->startElement('a:solidFill');

									// a:schemeClr
									$objWriter->startElement('a:schemeClr');
									$objWriter->writeAttribute('val', 'phClr');
									$objWriter->endElement();

								$objWriter->endElement();

								// a:prstDash
								$objWriter->startElement('a:prstDash');
								$objWriter->writeAttribute('val', 'solid');
								$objWriter->endElement();

							$objWriter->endElement();

						$objWriter->endElement();



						// a:effectStyleLst
						$objWriter->startElement('a:effectStyleLst');

							// a:effectStyle
							$objWriter->startElement('a:effectStyle');

								// a:effectLst
								$objWriter->startElement('a:effectLst');

									// a:outerShdw
									$objWriter->startElement('a:outerShdw');
									$objWriter->writeAttribute('blurRad', '40000');
									$objWriter->writeAttribute('dist', '20000');
									$objWriter->writeAttribute('dir', '5400000');
									$objWriter->writeAttribute('rotWithShape', '0');

										// a:srgbClr
										$objWriter->startElement('a:srgbClr');
										$objWriter->writeAttribute('val', '000000');

											// a:alpha
											$objWriter->startElement('a:alpha');
											$objWriter->writeAttribute('val', '38000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

							$objWriter->endElement();

							// a:effectStyle
							$objWriter->startElement('a:effectStyle');

								// a:effectLst
								$objWriter->startElement('a:effectLst');

									// a:outerShdw
									$objWriter->startElement('a:outerShdw');
									$objWriter->writeAttribute('blurRad', '40000');
									$objWriter->writeAttribute('dist', '23000');
									$objWriter->writeAttribute('dir', '5400000');
									$objWriter->writeAttribute('rotWithShape', '0');

										// a:srgbClr
										$objWriter->startElement('a:srgbClr');
										$objWriter->writeAttribute('val', '000000');

											// a:alpha
											$objWriter->startElement('a:alpha');
											$objWriter->writeAttribute('val', '35000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

							$objWriter->endElement();

							// a:effectStyle
							$objWriter->startElement('a:effectStyle');

								// a:effectLst
								$objWriter->startElement('a:effectLst');

									// a:outerShdw
									$objWriter->startElement('a:outerShdw');
									$objWriter->writeAttribute('blurRad', '40000');
									$objWriter->writeAttribute('dist', '23000');
									$objWriter->writeAttribute('dir', '5400000');
									$objWriter->writeAttribute('rotWithShape', '0');

										// a:srgbClr
										$objWriter->startElement('a:srgbClr');
										$objWriter->writeAttribute('val', '000000');

											// a:alpha
											$objWriter->startElement('a:alpha');
											$objWriter->writeAttribute('val', '35000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

								// a:scene3d
								$objWriter->startElement('a:scene3d');

									// a:camera
									$objWriter->startElement('a:camera');
									$objWriter->writeAttribute('prst', 'orthographicFront');

										// a:rot
										$objWriter->startElement('a:rot');
										$objWriter->writeAttribute('lat', '0');
										$objWriter->writeAttribute('lon', '0');
										$objWriter->writeAttribute('rev', '0');
										$objWriter->endElement();

									$objWriter->endElement();

									// a:lightRig
									$objWriter->startElement('a:lightRig');
									$objWriter->writeAttribute('rig', 'threePt');
									$objWriter->writeAttribute('dir', 't');

										// a:rot
										$objWriter->startElement('a:rot');
										$objWriter->writeAttribute('lat', '0');
										$objWriter->writeAttribute('lon', '0');
										$objWriter->writeAttribute('rev', '1200000');
										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

								// a:sp3d
								$objWriter->startElement('a:sp3d');

									// a:bevelT
									$objWriter->startElement('a:bevelT');
									$objWriter->writeAttribute('w', '63500');
									$objWriter->writeAttribute('h', '25400');
									$objWriter->endElement();

								$objWriter->endElement();

							$objWriter->endElement();

						$objWriter->endElement();

						// a:bgFillStyleLst
						$objWriter->startElement('a:bgFillStyleLst');

							// a:solidFill
							$objWriter->startElement('a:solidFill');

								// a:schemeClr
								$objWriter->startElement('a:schemeClr');
								$objWriter->writeAttribute('val', 'phClr');
								$objWriter->endElement();

							$objWriter->endElement();

							// a:gradFill
							$objWriter->startElement('a:gradFill');
							$objWriter->writeAttribute('rotWithShape', '1');

								// a:gsLst
								$objWriter->startElement('a:gsLst');

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '0');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:tint
											$objWriter->startElement('a:tint');
											$objWriter->writeAttribute('val', '40000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '350000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '40000');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:tint
											$objWriter->startElement('a:tint');
											$objWriter->writeAttribute('val', '45000');
											$objWriter->endElement();

											// a:shade
											$objWriter->startElement('a:shade');
											$objWriter->writeAttribute('val', '99000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '350000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '100000');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:shade
											$objWriter->startElement('a:shade');
											$objWriter->writeAttribute('val', '20000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '255000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

								// a:path
								$objWriter->startElement('a:path');
								$objWriter->writeAttribute('path', 'circle');

									// a:fillToRect
									$objWriter->startElement('a:fillToRect');
									$objWriter->writeAttribute('l', '50000');
									$objWriter->writeAttribute('t', '-80000');
									$objWriter->writeAttribute('r', '50000');
									$objWriter->writeAttribute('b', '180000');
									$objWriter->endElement();

								$objWriter->endElement();

							$objWriter->endElement();

							// a:gradFill
							$objWriter->startElement('a:gradFill');
							$objWriter->writeAttribute('rotWithShape', '1');

								// a:gsLst
								$objWriter->startElement('a:gsLst');

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '0');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:tint
											$objWriter->startElement('a:tint');
											$objWriter->writeAttribute('val', '80000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '300000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

									// a:gs
									$objWriter->startElement('a:gs');
									$objWriter->writeAttribute('pos', '100000');

										// a:schemeClr
										$objWriter->startElement('a:schemeClr');
										$objWriter->writeAttribute('val', 'phClr');

											// a:shade
											$objWriter->startElement('a:shade');
											$objWriter->writeAttribute('val', '30000');
											$objWriter->endElement();

											// a:satMod
											$objWriter->startElement('a:satMod');
											$objWriter->writeAttribute('val', '200000');
											$objWriter->endElement();

										$objWriter->endElement();

									$objWriter->endElement();

								$objWriter->endElement();

								// a:path
								$objWriter->startElement('a:path');
								$objWriter->writeAttribute('path', 'circle');

									// a:fillToRect
									$objWriter->startElement('a:fillToRect');
									$objWriter->writeAttribute('l', '50000');
									$objWriter->writeAttribute('t', '50000');
									$objWriter->writeAttribute('r', '50000');
									$objWriter->writeAttribute('b', '50000');
									$objWriter->endElement();

								$objWriter->endElement();

							$objWriter->endElement();

						$objWriter->endElement();

					$objWriter->endElement();
					}

				$objWriter->endElement();

				// a:objectDefaults
				$objWriter->writeElement('a:objectDefaults', null);

				// a:extraClrSchemeLst
				$objWriter->writeElement('a:extraClrSchemeLst', null);

			$objWriter->endElement();

			// Return
			return $objWriter->getData();
	}
}
