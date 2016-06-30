<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Model_Productquestionstore extends Mage_Core_Model_Abstract
{
  protected function _construct()
  {
    parent::_construct();
    $this->_init('productquestion/productquestionstore');
  }

  public function saveStoreView($stores, $questionId)
  {

    $questionStoreModel = Mage::getModel('productquestion/productquestionstore');
    $listQuestionOld = $questionStoreModel->getCollection()->AddFieldToFilter('productquestion_id', $questionId)->getData();
    $listStoreOld = array();
    foreach ($listQuestionOld as $quest) {
      $listStoreOld[] = $quest['store_id'];
    }
    $insert = array_diff($stores, $listStoreOld);
    $delete = array_diff($listStoreOld, $stores);
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    if (isset($stores)) {
      if (count($delete) > 0) {
        foreach ($delete as $del) {
          $where = 'productquestion_store.productquestion_id = ' . $questionId . ' AND productquestion_store.store_id = ' . $del;
          $writeConnection->delete('productquestion_store', $where);
        }
      }
      if (count($insert) > 0) {
        $data = array();
        foreach ($insert as $store) {
          $data[] = array(
            'productquestion_id' => $questionId,
            'store_id'           => $store,
          );
        }
        if (count($data) > 0) {
          $writeConnection->insertMultiple('productquestion_store', $data);
        }
      }
    }
  }

  public function saveStoreId($data)
  {
    if (count($data) > 0) {
      $resource = Mage::getSingleton('core/resource');
      $table = Mage::getSingleton('core/resource')->getTableName('productquestion/productquestionstore');
      $writeConnection = $resource->getConnection('core_write');
      $writeConnection->insertMultiple($table, $data);
    }
  }
}