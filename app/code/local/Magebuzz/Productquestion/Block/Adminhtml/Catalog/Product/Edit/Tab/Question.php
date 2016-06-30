<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Catalog_Product_Edit_Tab_Question extends Mage_Adminhtml_Block_Widget
{
  public function __construct()
  {
    parent::__construct();
    $product   = $this->_getProduct();
    $productId = $product->getEntityId();
    $questions = Mage::getModel('productquestion/productquestion')->getCollection();
    
    $productQuestions = Mage::getModel('productquestion/productquestionproduct')->getCollection()->addFieldToFilter('product_id', $productId)->getData();
    if (!empty($productQuestions)) {
      foreach ($productQuestions as $pid) {
        $listQuestionIds[] = $pid['productquestion_id'];
      }
      $questions->addFieldToFilter('productquestion_id', $listQuestionIds);
    }

    $this->setCollection($questions);
    $this->setTemplate('productquestion/catalog/product/edit/question.phtml');
  }

  protected function _getProduct()
  {
    $id = $this->getRequest()->getParam('id');
    $productCollection = Mage::getModel('catalog/product')->load($id);
    return $productCollection;
  }
  
  public function getProductName()
  {
    $product = $this->_getProduct();
    return $product->getName();
  }
}
