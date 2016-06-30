<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
  private $parent;

  protected function _prepareLayout()
  {
    parent::_prepareLayout();
    $this->addTab('question', array(
      'label'   => Mage::helper('productquestion')->__('Product Questions'),
      'content' => $this->_translateHtml($this->getLayout()
          ->createBlock('productquestion/adminhtml_catalog_product_edit_tab_question')->toHtml()),
    ));
    return $this;
  }
}