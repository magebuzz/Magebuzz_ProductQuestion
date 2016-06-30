<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Productquestion extends Mage_Core_Block_Template
{
  public function __construct()
  {
    parent::__construct();
    $_product = $this->getProduct()->getData();
    $storeId[] = Mage::app()->getStore()->getId();
    $storeId[] = 0;
    
    $questionProduct = Mage::getModel('productquestion/productquestionproduct')->getCollection()->addFieldToFilter('product_id', $_product['entity_id'])->getData();
    $questIds = array();
    $questId = '';
    if($questionProduct) {
      foreach ($questionProduct as $quest) {
        $questIds[] = $quest['productquestion_id'];
      }
    }
    if($questIds) {
      $listQuestionStore = Mage::getModel('productquestion/productquestionstore')->getCollection()
      ->addFieldToFilter('productquestion_id', $questIds)
      ->addFieldToFilter('store_id', $storeId);
       if(isset($listQuestionStore)) {
         foreach ($listQuestionStore as $listquest) {
          $questId[] = $listquest->getProductquestionId();
        }
       } 
    }
    $collection = Mage::getModel('productquestion/productquestion')->getCollection();
    $collection->setOrder('productquestion_id', 'DESC');
    $collection->addFieldToFilter('visibility', 1);
    $collection->addFieldToFilter('replied', 1);
    $collection->addFieldToFilter('productquestion_id', $questId);
    $this->setQuestion($collection);
  }

  protected function _prepareLayout()
  {
    parent::_prepareLayout();
    $pager = $this->getLayout()->createBlock('page/html_pager', 'productquestion.pager');
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
    if (!Mage::registry('product') && $this->getProductId()) {
      $product = Mage::getModel('catalog/product')->load($this->getProductId());
      Mage::register('product', $product);
    }
    return Mage::registry('product');
  }

  public function getPagerHtml()
  {
    return $this->getChildHtml('pager');

  }

  public function getProductQuestionUrl()
  {
    return Mage::getUrl('productquestion/index/index', array(
      'id'       => $this->getProduct()->getId(),
      'category' => $this->getProduct()->getCategoryId()
    ));
  }

  public function getFormData()
  {
    return Mage::getSingleton('productquestion/session')->getFormData(TRUE);
  }

  public function getCookie($name)
  {
    return Mage::getModel('core/cookie')->get($name);
  }

  public function isShownInProductDetail() {
    return Mage::getStoreConfig('productquestion/general/show_in_product_detail', Mage::app()->getStore());
  }

  public function isGuestAllowedToAsk() {
    return Mage::getStoreConfig('productquestion/general/allow_guest_ask_question', Mage::app()->getStore());
  }
}