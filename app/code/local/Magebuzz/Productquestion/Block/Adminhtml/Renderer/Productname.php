<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Renderer_Productname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    $productquestionId = $row->getProductquestionId();
    $productquestionProduct = Mage::getModel('productquestion/productquestionproduct')->getCollection()
      ->addFieldToFilter('productquestion_id', $productquestionId);
    $productName = '';
    if (count($productquestionProduct)) {
      foreach ($productquestionProduct as $_productquestionProduct) {
        $productId = $_productquestionProduct->getProductId();
        $productName .= Mage::getModel('catalog/product')->load($productId)->getName();
      }
    }
    return $productName;
  }
}