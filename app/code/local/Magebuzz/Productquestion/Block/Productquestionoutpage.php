<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Productquestionoutpage extends Mage_Catalog_Block_Product_View
{
  public function __construct()
  {
    parent::__construct();
    $params = $this->getRequest()->getParams();
    $storeId[] = Mage::app()->getStore()->getId();
    $storeId[] = 0;
    $questions = Mage::getModel('productquestion/productquestionproduct')->getCollection()->addFieldToFilter('product_id', $params['id'])->getData();
    
    $collection = Mage::getModel('productquestion/productquestion')->getCollection();
    $collection->setOrder('productquestion_id', 'DESC');
    $collection->addFieldToFilter('visibility', 1);
    $collection->addFieldToFilter('replied', 1);

    if (!empty($questions)) {
      foreach ($questions as $question) {
        $questIds[] = $question['productquestion_id'];
      }

      $questionStoreLists = Mage::getModel('productquestion/productquestionstore')->getCollection()
        ->addFieldToFilter('productquestion_id', $questIds)
        ->addFieldToFilter('store_id', $storeId);

      foreach ($questionStoreLists as $list) {
        $questId[] = $list->getProductquestionId();
      }

      $collection->addFieldToFilter('productquestion_id', $questId);
    } else {
      $collection->addFieldToFilter('productquestion_id', 0);
    }
    
    $this->setQuestion($collection);
  }

  protected function _prepareLayout()
  {
    parent::_prepareLayout();
    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('productquestion')->__('Product Question'));
    $pager = $this->getLayout()->createBlock('page/html_pager', 'productquestionoutpage.pager');
    $pager->setAvailableLimit(array(3 => 3, 6 => 6, 9 => 9, 'all' => 'all'));
    $pager->setCollection($this->getQuestion());
    $this->setChild('pager', $pager);
    return $this;
  }

  public function getPostUrl()
  {
    return $this->getUrl('productquestion/index/post', array());
  }

  public function isCustomerLoggedIn()
  {
    return Mage::getSingleton('customer/session')->isLoggedIn();
  }

  public function getCurrentCustomer()
  {
    return Mage::getSingleton('customer/session')->getCustomer();
  }

  public function getProduct()
  {
    $params = $this->getRequest()->getParams();
    $product = Mage::getModel('catalog/product')->load($params['id']);

    return $product;
  }

  public function getPagerHtml()
  {
    return $this->getChildHtml('pager');
  }

  public function getFormData()
  {
    return Mage::getSingleton('productquestion/session')->getFormData(TRUE);
  }

  public function getCookie($name)
  {
    return Mage::getModel('core/cookie')->get($name);
  }
}