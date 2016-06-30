<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Model_System_Config_Source_Dropdown_Theme
{
  public function toOptionArray()
  {
    return array(
      array(
        'value' => 'light',
        'label' => 'Light (default)',
      ),
      array(
        'value' => 'dark',
        'label' => 'Dark',
      ),
    );
  }
}