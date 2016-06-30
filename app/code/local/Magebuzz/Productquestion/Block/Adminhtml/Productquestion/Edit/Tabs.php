<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Productquestion_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
    parent::__construct();
    $this->setId('productquestion_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle(Mage::helper('productquestion')->__('Question'));
  }

  protected function _beforeToHtml()
  {
    $question = Mage::registry('productquestion_data')->getData();
    $this->addTab('form_section', array(
      'label'   => Mage::helper('productquestion')->__('Details'),
      'title'   => Mage::helper('productquestion')->__('Details'),
      'content' => $this->getLayout()->createBlock('productquestion/adminhtml_productquestion_edit_tab_form')->toHtml(),
    ));

    $this->addTab('product_section', array(
      'label'   => Mage::helper('productquestion')->__('Product'),
      'title'   => Mage::helper('productquestion')->__('Product'),
      'content' => $this->getLayout()->createBlock('productquestion/adminhtml_productquestion_edit_tab_product')->toHtml(),
    ));
    return parent::_beforeToHtml();
  }
}