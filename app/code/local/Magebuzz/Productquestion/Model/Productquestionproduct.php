<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Model_Productquestionproduct extends Mage_Core_Model_Abstract
{
  protected function _construct()
  {
    parent::_construct();
    $this->_init('productquestion/productquestionproduct');
  }
}