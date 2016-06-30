<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Model_Productquestion extends Mage_Core_Model_Abstract
{
  protected function _construct()
  {
    parent::_construct();
    $this->_init('productquestion/productquestion');
  }

  public function compareProductList($newArray, $oldArray, $questionId)
  {
    $productModle = Mage::getModel('catalog/product');
    $insert = array_diff($newArray, $oldArray);
    $delete = array_diff($oldArray, $newArray);
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    if (isset($newArray)) {
      if ($delete) {
        foreach ($delete as $del) {
          $where = 'productquestion_product.productquestion_id = ' . $questionId . ' AND productquestion_product.product_id = ' . $del;
          $writeConnection->delete('productquestion_product', $where);
        }
      }
      if ($insert) {
        $data = array();
        foreach ($insert as $pid) {

          $data[] = array(
            'productquestion_id' => $questionId,
            'product_id'         => $pid,
          );
        }
        if (count($data) > 0) {
          $writeConnection->insertMultiple('productquestion_product', $data);
        }
      }
    } else {
      if ($oldArray) {
        foreach ($oldArray as $del) {
          $where = 'productquestion_product.productquestion_id = ' . $questionId . ' AND productquestion_product.product_id = ' . $del;
          $writeConnection->delete('productquestion_product', $where);
        }
      }
    }
  }
}