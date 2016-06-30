<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Model_Status extends Varien_Object
{
  const STATUS_PUBLIC = 1;
  const STATUS_PRIVATE = 0;

  static public function getOptionArray()
  {
    return array(
      self::STATUS_PUBLIC  => Mage::helper('productquestion')->__('Public'),
      self::STATUS_PRIVATE => Mage::helper('productquestion')->__('Private')
    );
  }
}