<?php
require_once 'Properties.php';
/**
 * Created by PhpStorm.
 * User: Wiktor Trzonkowski
 * Date: 7/2/14
 * Time: 2:36 PM
 */

class PHPExcel_Chart_Gridlines extends
  PHPExcel_Properties {

  /**
   * Properties of Class:
   * Object State (State for Minor Tick Mark) @var bool
   * Line Properties @var  array of mixed
   * Shadow Properties @var  array of mixed
   * Glow Properties @var  array of mixed
   * Soft Properties @var  array of mixed
   *
   */

  private
      $_object_state = FALSE,
      $_line_properties = array(
          'color' => array(
              'type' => self::EXCEL_COLOR_TYPE_STANDARD,
              'value' => NULL,
              'alpha' => 0
          ),
          'style' => array(
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
          )
      ),
      $_shadow_properties = array(
          'presets' => self::SHADOW_PRESETS_NOSHADOW,
          'effect' => NULL,
          'color' => array(
              'type' => self::EXCEL_COLOR_TYPE_STANDARD,
              'value' => 'black',
              'alpha' => 85,
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
      ),
      $_glow_properties = array(
          'size' => NULL,
          'color' => array(
              'type' => self::EXCEL_COLOR_TYPE_STANDARD,
              'value' => 'black',
              'alpha' => 40
          )
      ),
      $_soft_edges = array(
          'size' => NULL
      );

  /**
   * Get Object State
   *
   * @return bool
   */

  public function getObjectState() {
    return $this->_object_state;
  }

  /**
   * Change Object State to True
   *
   * @return PHPExcel_Chart_Gridlines
   */

  private function _activateObject() {
    $this->_object_state = TRUE;

    return $this;
  }

  /**
   * Set Line Color Properties
   *
   * @param string $value
   * @param int $alpha
   * @param string $type
   */

  public function setLineColorProperties($value, $alpha = 0, $type = self::EXCEL_COLOR_TYPE_STANDARD) {
    $this
        ->_activateObject()
        ->_line_properties['color'] = $this->setColorProperties(
        $value,
        $alpha,
        $type);
  }

  /**
   * Set Line Color Properties
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
   */

  public function setLineStyleProperties($line_width = NULL, $compound_type = NULL, $dash_type = NULL, $cap_type = NULL, $join_type = NULL, $head_arrow_type = NULL, $head_arrow_size = NULL, $end_arrow_type = NULL, $end_arrow_size = NULL) {
    $this->_activateObject();
    (!is_null($line_width))
        ? $this->_line_properties['style']['width'] = $this->getExcelPointsWidth((float) $line_width)
        : NULL;
    (!is_null($compound_type))
        ? $this->_line_properties['style']['compound'] = (string) $compound_type
        : NULL;
    (!is_null($dash_type))
        ? $this->_line_properties['style']['dash'] = (string) $dash_type
        : NULL;
    (!is_null($cap_type))
        ? $this->_line_properties['style']['cap'] = (string) $cap_type
        : NULL;
    (!is_null($join_type))
        ? $this->_line_properties['style']['join'] = (string) $join_type
        : NULL;
    (!is_null($head_arrow_type))
        ? $this->_line_properties['style']['arrow']['head']['type'] = (string) $head_arrow_type
        : NULL;
    (!is_null($head_arrow_size))
        ? $this->_line_properties['style']['arrow']['head']['size'] = (string) $head_arrow_size
        : NULL;
    (!is_null($end_arrow_type))
        ? $this->_line_properties['style']['arrow']['end']['type'] = (string) $end_arrow_type
        : NULL;
    (!is_null($end_arrow_size))
        ? $this->_line_properties['style']['arrow']['end']['size'] = (string) $end_arrow_size
        : NULL;
  }

  /**
   * Get Line Color Property
   *
   * @param string $parameter
   *
   * @return string
   */

  public function getLineColorProperty($parameter) {
    return $this->_line_properties['color'][$parameter];
  }

  /**
   * Get Line Style Property
   *
   * @param  array|string $elements
   *
   * @return string
   */

  public function getLineStyleProperty($elements) {
    return $this->getArrayElementsValue($this->_line_properties['style'], $elements);
  }

  /**
   * Set Glow Properties
   *
   * @param  float $size
   * @param  string $color_value
   * @param  int $color_alpha
   * @param  string $color_type
   *
   */

  public function setGlowProperties($size, $color_value = NULL, $color_alpha = NULL, $color_type = NULL) {
    $this
        ->_activateObject()
        ->_setGlowSize($size)
        ->_setGlowColor($color_value, $color_alpha, $color_type);
  }

  /**
   * Get Glow Color Property
   *
   * @param string $property
   *
   * @return string
   */

  public function getGlowColor($property) {
    return $this->_glow_properties['color'][$property];
  }

  /**
   * Get Glow Size
   *
   * @return string
   */

  public function getGlowSize() {
    return $this->_glow_properties['size'];
  }

  /**
   * Set Glow Size
   *
   * @param float $size
   *
   * @return PHPExcel_Chart_Gridlines
   */

  private function _setGlowSize($size) {
    $this->_glow_properties['size'] = $this->getExcelPointsWidth((float) $size);

    return $this;
  }

  /**
   * Set Glow Color
   *
   * @param string $color
   * @param int $alpha
   * @param string $type
   *
   * @return PHPExcel_Chart_Gridlines
   */

  private function _setGlowColor($color, $alpha, $type) {
    if (!is_null($color)) {
      $this->_glow_properties['color']['value'] = (string) $color;
    }
    if (!is_null($alpha)) {
      $this->_glow_properties['color']['alpha'] = $this->getTrueAlpha((int) $alpha);
    }
    if (!is_null($type)) {
      $this->_glow_properties['color']['type'] = (string) $type;
    }

    return $this;
  }

  /**
   * Get Line Style Arrow Parameters
   *
   * @param string $arrow_selector
   * @param string $property_selector
   *
   * @return string
   */

  public function getLineStyleArrowParameters($arrow_selector, $property_selector) {
    return $this->getLineStyleArrowSize($this->_line_properties['style']['arrow'][$arrow_selector]['size'], $property_selector);
  }

  /**
   * Set Shadow Properties
   *
   * @param int $sh_presets
   * @param string $sh_color_value
   * @param string $sh_color_type
   * @param int $sh_color_alpha
   * @param string $sh_blur
   * @param int $sh_angle
   * @param float $sh_distance
   *
   */

  public function setShadowProperties($sh_presets, $sh_color_value = NULL, $sh_color_type = NULL, $sh_color_alpha = NULL, $sh_blur = NULL, $sh_angle = NULL, $sh_distance = NULL) {
    $this
        ->_activateObject()
        ->_setShadowPresetsProperties((int) $sh_presets)
        ->_setShadowColor(
            is_null($sh_color_value) ? $this->_shadow_properties['color']['value'] : $sh_color_value
            , is_null($sh_color_alpha) ? (int) $this->_shadow_properties['color']['alpha']
                : $this->getTrueAlpha($sh_color_alpha)
            , is_null($sh_color_type) ? $this->_shadow_properties['color']['type'] : $sh_color_type)
        ->_setShadowBlur($sh_blur)
        ->_setShadowAngle($sh_angle)
        ->_setShadowDistance($sh_distance);
  }

  /**
   * Set Shadow Presets Properties
   *
   * @param int $shadow_presets
   *
   * @return PHPExcel_Chart_Gridlines
   */

  private function _setShadowPresetsProperties($shadow_presets) {
    $this->_shadow_properties['presets'] = $shadow_presets;
    $this->_setShadowProperiesMapValues($this->getShadowPresetsMap($shadow_presets));

    return $this;
  }

  /**
   * Set Shadow Properties Values
   *
   * @param array $properties_map
   * @param * $reference
   *
   * @return PHPExcel_Chart_Gridlines
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
   * @return PHPExcel_Chart_Gridlines
   */

  private function _setShadowColor($color, $alpha, $type) {
    if (!is_null($color)) {
      $this->_shadow_properties['color']['value'] = (string) $color;
    }
    if (!is_null($alpha)) {
      $this->_shadow_properties['color']['alpha'] = $this->getTrueAlpha((int) $alpha);
    }
    if (!is_null($type)) {
      $this->_shadow_properties['color']['type'] = (string) $type;
    }

    return $this;
  }

  /**
   * Set Shadow Blur
   *
   * @param float $blur
   *
   * @return PHPExcel_Chart_Gridlines
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
   * @return PHPExcel_Chart_Gridlines
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
   * @return PHPExcel_Chart_Gridlines
   */

  private function _setShadowDistance($distance) {
    if ($distance !== NULL) {
      $this->_shadow_properties['distance'] = (string) $this->getExcelPointsWidth($distance);
    }

    return $this;
  }

  /**
   * Get Shadow Property
   *
   * @param string $elements
   * @param array $elements
   *
   * @return string
   */

  public function getShadowProperty($elements) {
    return $this->getArrayElementsValue($this->_shadow_properties, $elements);
  }

  /**
   * Set Soft Edges Size
   *
   * @param float $size
   */

  public function setSoftEdgesSize($size) {
    if (!is_null($size)) {
      $this->_activateObject();
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