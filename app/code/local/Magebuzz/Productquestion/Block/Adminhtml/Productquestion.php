<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Productquestion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_productquestion';
    $this->_blockGroup = 'productquestion';
    $this->_headerText = Mage::helper('productquestion')->__('Product Questions');
    $this->_addButtonLabel = Mage::helper('productquestion')->__('Add Question');
    parent::__construct();
  }
}