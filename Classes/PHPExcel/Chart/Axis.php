<?php
require_once 'Properties.php';

/**
 * Created by PhpStorm.
 * User: Wiktor Trzonkowski
 * Date: 6/17/14
 * Time: 12:11 PM
 */

class PHPExcel_Chart_Axis extends
  PHPExcel_Properties {

  /**
   * Axis Number
   *
   * @var  array of mixed
   */

  private
      $_axis_number = array(
      'format' => self::FORMAT_CODE_GENERAL,
      'source_linked' => 1
  );

  /**
   * Axis Options
   *
   * @var  array of mixed
   */

  private $_axis_options = array(
      'minimum' => NULL,
      'maximum' => NULL,
      'major_unit' => NULL,
      'minor_unit' => NULL,
      'orientation' => self::ORIENTATION_NORMAL,
      'minor_tick_mark' => self::TICK_MARK_NONE,
      'major_tick_mark' => self::TICK_MARK_NONE,
      'axis_labels' => self::AXIS_LABELS_NEXT_TO,
      'horizontal_crosses' => self::HORIZONTAL_CROSSES_AUTOZERO,
      'horizontal_crosses_value' => NULL
  );

  /**
   * Fill Properties
   *
   * @var  array of mixed
   */

  private $_fill_properties = array(
      'type' => self::EXCEL_COLOR_TYPE_ARGB,
      'value' => NULL,
      'alpha' => 0
  );

  /**
   * Line Properties
   *
   * @var  array of mixed
   */

  private $_line_properties = array(
      'type' => self::EXCEL_COLOR_TYPE_ARGB,
      'value' => NULL,
      'alpha' => 0
  );

  /**
   * Line Style Properties
   *
   * @var  array of mixed
   */

  private $_line_style_properties = array(
      'width' => '9525',
      'compound' => self::LINE_STYLE_COMPOUND_SIMPLE,
      'dash' => self::LINE_STYLE_DASH_SOLID,
      'cap' => self::LINE_STYLE_CAP_FLAT,
      'join' => self::LINE_STYLE_JOIN_BEVEL,
      'arrow' => array(
          'head' => array(
              'type' => self::LINE_STYLE_ARROW_TYPE_NOARROW,
              'size' => self::LINE_STYLE_ARROW_SIZE_5
          ),
          'end' => array(
              'type' => self::LINE_STYLE_ARROW_TYPE_NOARROW,
              'size' => self::LINE_STYLE_ARROW_SIZE_8
          ),
      )
  );

  /**
   * Shadow Properties
   *
   * @var  array of mixed
   */

  private $_shadow_properties = array(
      'presets' => self::SHADOW_PRESETS_NOSHADOW,
      'effect' => NULL,
      'color' => array(
          'type' => self::EXCEL_COLOR_TYPE_STANDARD,
          'value' => 'black',
          'alpha' => 40,
      ),
      'size' => array(
          'sx' => NULL,
          'sy' => NULL,
          'kx' => NULL
      ),
      'blur' => NULL,
      'direction' => NULL,
      'distance' => NULL,
      'algn' => NULL,
      'rotWithShape' => NULL
  );

  /**
   * Glow Properties
   *
   * @var  array of mixed
   */

  private $_glow_properties = array(
      'size' => NULL,
      'color' => array(
          'type' => self::EXCEL_COLOR_TYPE_STANDARD,
          'value' => 'black',
          'alpha' => 40
      )
  );

  /**
   * Soft Edge Properties
   *
   * @var  array of mixed
   */

  private $_soft_edges = array(
      'size' => NULL
  );

  /**
   * Get Series Data Type
   *
   * @return  string
   */

  public function setAxisNumberProperties($format_code) {
    $this->_axis_number['format'] = (string) $format_code;
    $this->_axis_number['source_linked'] = 0;
  }

  /**
   * Get Axis Number Format Data Type
   *
   * @return  string
   */

  public function getAxisNumberFormat() {
    return $this->_axis_number['format'];
  }

  /**
   * Get Axis Number Source Linked
   *
   * @return  string
   */

  public function getAxisNumberSourceLinked() {
    return (string) $this->_axis_number['source_linked'];
  }

  /**
   * Set Axis Options Properties
   *
   * @param string $axis_labels
   * @param string $horizontal_crosses_value
   * @param string $horizontal_crosses
   * @param string $axis_orientation
   * @param string $major_tmt
   * @param string $minor_tmt
   * @param string $minimum
   * @param string $maximum
   * @param string $major_unit
   * @param string $minor_unit
   *
   */

  public function setAxisOptionsProperties($axis_labels, $horizontal_crosses_value = NULL, $horizontal_crosses = NULL,
      $axis_orientation = NULL, $major_tmt = NULL, $minor_tmt = NULL, $minimum = NULL, $maximum = NULL, $major_unit = NULL,
      $minor_unit = NULL) {

    $this->_axis_options['axis_labels'] = (string) $axis_labels;
    ($horizontal_crosses_value !== NULL)
        ? $this->_axis_options['horizontal_crosses_value'] = (string) $horizontal_crosses_value : NULL;
    ($horizontal_crosses !== NULL) ? $this->_axis_options['horizontal_crosses'] = (string) $horizontal_crosses : NULL;
    ($axis_orientation !== NULL) ? $this->_axis_options['orientation'] = (string) $axis_orientation : NULL;
    ($major_tmt !== NULL) ? $this->_axis_options['major_tick_mark'] = (string) $major_tmt : NULL;
    ($minor_tmt !== NULL) ? $this->_axis_options['minor_tick_mark'] = (string) $minor_tmt : NULL;
    ($minor_tmt !== NULL) ? $this->_axis_options['minor_tick_mark'] = (string) $minor_tmt : NULL;
    ($minimum !== NULL) ? $this->_axis_options['minimum'] = (string) $minimum : NULL;
    ($maximum !== NULL) ? $this->_axis_options['maximum'] = (string) $maximum : NULL;
    ($major_unit !== NULL) ? $this->_axis_options['major_unit'] = (string) $major_unit : NULL;
    ($minor_unit !== NULL) ? $this->_axis_options['minor_unit'] = (string) $minor_unit : NULL;
  }

  /**
   * Get Axis Options Property
   *
   * @param string $property
   *
   * @return string
   */

  public function getAxisOptionsProperty($property) {
    return $this->_axis_options[$property];
  }

  /**
   * Set Axis Orientation Property
   *
   * @param string $orientation
   *
   */

  public function setAxisOrientation($orientation) {
    $this->orientation = (string) $orientation;
  }

  /**
   * Set Fill Property
   *
   * @param string $color
   * @param int $alpha
   * @param string $type
   *
   */

  public function setFillParameters($color, $alpha = 0, $type = self::EXCEL_COLOR_TYPE_ARGB) {
    $this->_fill_properties = $this->setColorProperties($color, $alpha, $type);
  }

  /**
   * Set Line Property
   *
   * @param string $color
   * @param int $alpha
   * @param string $type
   *
   */

  public function setLineParameters($color, $alpha = 0, $type = self::EXCEL_COLOR_TYPE_ARGB) {
    $this->_line_properties = $this->setColorProperties($color, $alpha, $type);
  }

  /**
   * Get Fill Property
   *
   * @param string $property
   *
   * @return string
   */

  public function getFillProperty($property) {
    return $this->_fill_properties[$property];
  }

  /**
   * Get Line Property
   *
   * @param string $property
   *
   * @return string
   */

  public function getLineProperty($property) {
    return $this->_line_properties[$property];
  }

  /**
   * Set Line Style Properties
   *
   * @param float $line_width
   * @param string $compound_type
   * @param string $dash_type
   * @param string $cap_type
   * @param string $join_type
   * @param string $head_arrow_type
   * @param string $head_arrow_size
   * @param string $end_arrow_type
   * @param string $end_arrow_size
   *
   */

  public function setLineStyleProperties($line_width = NULL, $compound_type = NULL,
      $dash_type = NULL, $cap_type = NULL, $join_type = NULL, $head_arrow_type = NULL,
      $head_arrow_size = NULL, $end_arrow_type = NULL, $end_arrow_size = NULL) {

    (!is_null($line_width)) ? $this->_line_style_properties['width'] = $this->getExcelPointsWidth((float) $line_width)
        : NULL;
    (!is_null($compound_type)) ? $this->_line_style_properties['compound'] = (string) $compound_type : NULL;
    (!is_null($dash_type)) ? $this->_line_style_properties['dash'] = (string) $dash_type : NULL;
    (!is_null($cap_type)) ? $this->_line_style_properties['cap'] = (string) $cap_type : NULL;
    (!is_null($join_type)) ? $this->_line_style_properties['join'] = (string) $join_type : NULL;
    (!is_null($head_arrow_type)) ? $this->_line_style_properties['arrow']['head']['type'] = (string) $head_arrow_type
        : NULL;
    (!is_null($head_arrow_size)) ? $this->_line_style_properties['arrow']['head']['size'] = (string) $head_arrow_size
        : NULL;
    (!is_null($end_arrow_type)) ? $this->_line_style_properties['arrow']['end']['type'] = (string) $end_arrow_type
        : NULL;
    (!is_null($end_arrow_size)) ? $this->_line_style_properties['arrow']['end']['size'] = (string) $end_arrow_size
        : NULL;
  }

  /**
   * Get Line Style Property
   *
   * @param array|string $elements
   *
   * @return string
   */

  public function getLineStyleProperty($elements) {
    return $this->getArrayElementsValue($this->_line_style_properties, $elements);
  }

  /**
   * Get Line Style Arrow Excel Width
   *
   * @param string $arrow
   *
   * @return string
   */

  public function getLineStyleArrowWidth($arrow) {
    return $this->getLineStyleArrowSize($this->_line_style_properties['arrow'][$arrow]['size'], 'w');
  }

  /**
   * Get Line Style Arrow Excel Length
   *
   * @param string $arrow
   *
   * @return string
   */

  public function getLineStyleArrowLength($arrow) {
    return $this->getLineStyleArrowSize($this->_line_style_properties['arrow'][$arrow]['size'], 'len');
  }

  /**
   * Set Shadow Properties
   *
   * @param int $shadow_presets
   * @param string $sh_color_value
   * @param string $sh_color_type
   * @param string $sh_color_alpha
   * @param float $sh_blur
   * @param int $sh_angle
   * @param float $sh_distance
   *
   */

  public function setShadowProperties($sh_presets, $sh_color_value = NULL, $sh_color_type = NULL, $sh_color_alpha = NULL, $sh_blur = NULL, $sh_angle = NULL, $sh_distance = NULL) {
    $this
        ->_setShadowPresetsProperties((int) $sh_presets)
        ->_setShadowColor(
            is_null($sh_color_value) ? $this->_shadow_properties['color']['value'] : $sh_color_value
            , is_null($sh_color_alpha) ? (int) $this->_shadow_properties['color']['alpha'] : $sh_color_alpha
            , is_null($sh_color_type) ? $this->_shadow_properties['color']['type'] : $sh_color_type)
        ->_setShadowBlur($sh_blur)
        ->_setShadowAngle($sh_angle)
        ->_setShadowDistance($sh_distance);
  }

  /**
   * Set Shadow Color
   *
   * @param int $shadow_presets
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setShadowPresetsProperties($shadow_presets) {
    $this->_shadow_properties['presets'] = $shadow_presets;
    $this->_setShadowProperiesMapValues($this->getShadowPresetsMap($shadow_presets));

    return $this;
  }

  /**
   * Set Shadow Properties from Maped Values
   *
   * @param array $properties_map
   * @param * $reference
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setShadowProperiesMapValues(array $properties_map, &$reference = NULL) {
    $base_reference = $reference;
    foreach ($properties_map as $property_key => $property_val) {
      if (is_array($property_val)) {
        if ($reference === NULL) {
          $reference = & $this->_shadow_properties[$property_key];
        } else {
          $reference = & $reference[$property_key];
        }
        $this->_setShadowProperiesMapValues($property_val, $reference);
      } else {
        if ($base_reference === NULL) {
          $this->_shadow_properties[$property_key] = $property_val;
        } else {
          $reference[$property_key] = $property_val;
        }
      }
    }

    return $this;
  }

  /**
   * Set Shadow Color
   *
   * @param string $color
   * @param int $alpha
   * @param string $type
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setShadowColor($color, $alpha, $type) {
    $this->_shadow_properties['color'] = $this->setColorProperties($color, $alpha, $type);

    return $this;
  }

  /**
   * Set Shadow Blur
   *
   * @param float $blur
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setShadowBlur($blur) {
    if ($blur !== NULL) {
      $this->_shadow_properties['blur'] = (string) $this->getExcelPointsWidth($blur);
    }

    return $this;
  }

  /**
   * Set Shadow Angle
   *
   * @param int $angle
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setShadowAngle($angle) {
    if ($angle !== NULL) {
      $this->_shadow_properties['direction'] = (string) $this->getExcelPointsAngle($angle);
    }

    return $this;
  }

  /**
   * Set Shadow Distance
   *
   * @param float $distance
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setShadowDistance($distance) {
    if ($distance !== NULL) {
      $this->_shadow_properties['distance'] = (string) $this->getExcelPointsWidth($distance);
    }

    return $this;
  }

  /**
   * Get Glow Property
   *
   * @param float $size
   * @param string $color_value
   * @param int $color_alpha
   * @param string $color_type
   */

  public function getShadowProperty($elements) {
    return $this->getArrayElementsValue($this->_shadow_properties, $elements);
  }

  /**
   * Set Glow Properties
   *
   * @param float $size
   * @param string $color_value
   * @param int $color_alpha
   * @param string $color_type
   */

  public function setGlowProperties($size, $color_value = NULL, $color_alpha = NULL, $color_type = NULL) {
    $this
        ->_setGlowSize($size)
        ->_setGlowColor(
            is_null($color_value) ? $this->_glow_properties['color']['value'] : $color_value
            , is_null($color_alpha) ? (int) $this->_glow_properties['color']['alpha'] : $color_alpha
            , is_null($color_type) ? $this->_glow_properties['color']['type'] : $color_type);
  }

  /**
   * Get Glow Property
   *
   * @param array|string $property
   *
   * @return string
   */

  public function getGlowProperty($property) {
    return $this->getArrayElementsValue($this->_glow_properties, $property);
  }

  /**
   * Set Glow Color
   *
   * @param float $size
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setGlowSize($size) {
    if (!is_null($size)) {
      $this->_glow_properties['size'] = $this->getExcelPointsWidth($size);
    }

    return $this;
  }

  /**
   * Set Glow Color
   *
   * @param string $color
   * @param int $alpha
   * @param string $type
   *
   * @return PHPExcel_Chart_Axis
   */

  private function _setGlowColor($color, $alpha, $type) {
    $this->_glow_properties['color'] = $this->setColorProperties($color, $alpha, $type);

    return $this;
  }

  /**
   * Set Soft Edges Size
   *
   * @param float $size
   */

  public function setSoftEdges($size) {
    if (!is_null($size)) {
      $_soft_edges['size'] = (string) $this->getExcelPointsWidth($size);
    }
  }

  /**
   * Get Soft Edges Size
   *
   * @return string
   */

  public function getSoftEdgesSize() {
    return $this->_soft_edges['size'];
  }
}