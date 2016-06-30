<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Productquestion_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('entity_id');
    $this->setDefaultSort('entity_id');
    $this->setDefaultDir('DESC');
    $this->setSaveParametersInSession(TRUE);
    $this->setUseAjax(TRUE);
  }

  protected function _prepareCollection()
  {
    $collection = Mage::getModel('catalog/product')->getCollection();
    $collection->setOrder('entity_id', 'DESC')
      ->addAttributeToSelect('sku')
      ->addAttributeToSelect('name')
      ->addAttributeToSelect('attribute_set_id')
      ->addAttributeToSelect('type_id');
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }
  
  protected function _addColumnFilterToCollection($column) {
    if ($column->getId() == 'entity_id') {
      $productIds = $this->getProductIds();
      if (empty($productIds)) {
        $productIds = 0;
      }
      
      if ($column->getFilter()->getValue()) {
        $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
      }
      else {
        if ($productIds) {
          $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
        }
      }
    }
    else {
      parent::_addColumnFilterToCollection($column);
    }
    return $this;
  }

  protected function _prepareColumns()
  {

    $this->addColumn('entity_id', array(
      'header_css_class' => 'a-center',
      'header'           => Mage::helper('productquestion')->__('ID'),
      'field_name'       => 'productIds[]',
      'align'            => 'center',
      'type'             => 'checkbox',
      'width'            => '50px',
      'index'            => 'entity_id',
      'values'           => $this->getProductIds()
    ));

    $this->addColumn('name', array(
      'header' => Mage::helper('productquestion')->__('Name'),
      'align'  => 'left',
      'index'  => 'name',
      'type'   => 'text',
    ));

    $this->addColumn('type',
      array(
        'header'  => Mage::helper('catalog')->__('Product Type'),
        'width'   => '60px',
        'index'   => 'type_id',
        'type'    => 'options',
        'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
      ));

    $this->addColumn('sku', array(
      'header' => Mage::helper('productquestion')->__('Sku'),
      'align'  => 'left',
      'index'  => 'sku',
    ));
    return parent::_prepareColumns();
  }

  public function getProductIds()
  {
    //$data = Mage::registry('productquestion_data')->getData();
    $params = $this->getRequest()->getParams();
    $questionId = 0;
    /* if (isset($data['productquestion_id'])) {
      $questionId = $data['productquestion_id'];
    } */
    if (isset($params['id'])) {
      $questionId = $params['id'];
    }
    if ($questionId > 0) {
      $questionModel = Mage::getSingleton('productquestion/productquestionproduct')->getCollection()
        ->AddFieldToFilter('productquestion_id', $questionId)->getData();
      if($questionModel) {
        foreach ($questionModel as $question) {
          $productIds[] = $question['product_id'];
        }
        return $productIds;
      }
    } else {
      return;
    }
  }
  
  public function getGridUrl() {
    return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/productGrid', array('_current' => TRUE));
  }

}
