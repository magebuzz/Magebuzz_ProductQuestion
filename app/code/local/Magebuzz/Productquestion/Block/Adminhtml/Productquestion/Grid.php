<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Productquestion_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('productquestionGrid');
    $this->setDefaultSort('productquestion_id');
    $this->setDefaultDir('DESC');
    $this->setSaveParametersInSession(TRUE);
  }

  protected function _prepareCollection()
  {
    $collection = Mage::getModel('productquestion/productquestion')->getCollection();
    $collection->setOrder('productquestion_id', 'DESC');
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $this->addColumn('date', array(
      'header' => Mage::helper('productquestion')->__('Date'),
      'align'  => 'left',
      'type'   => 'date',
      'index'  => 'date',
    ));

    $this->addColumn('replied', array(
      'header'  => Mage::helper('productquestion')->__('Replied'),
      'align'   => 'left',
      'width'   => '80px',
      'index'   => 'replied',
      'type'    => 'options',
      'options' => array(
        0 => 'No',
        1 => 'Yes',
      ),
    ));

    $this->addColumn('author_name', array(
      'header' => Mage::helper('productquestion')->__('Author Name'),
      'align'  => 'left',
      'index'  => 'author_name',
    ));

    $this->addColumn('author_email', array(
      'header' => Mage::helper('productquestion')->__('Author Email'),
      'align'  => 'left',
      'index'  => 'author_email',
    ));

    $this->addColumn('question', array(
      'header' => Mage::helper('productquestion')->__('Questions List'),
      'align'  => 'left',
      'index'  => 'question',
    ));
    $this->addColumn('product_name', array(
      'header'   => Mage::helper('productquestion')->__('Product Name'),
      'align'    => 'left',
      'renderer' => 'productquestion/adminhtml_renderer_productname',
      'filter' => false,
    ));

    $this->addColumn('visibility', array(
      'header'  => Mage::helper('productquestion')->__('Visibility'),
      'align'   => 'left',
      'width'   => '80px',
      'index'   => 'visibility',
      'type'    => 'options',
      'options' => array(
        0 => 'Private',
        1 => 'Public',
      ),
    ));

    $this->addColumn('action',
      array(
        'header'    => Mage::helper('productquestion')->__('Action'),
        'width'     => '100',
        'type'      => 'action',
        'getter'    => 'getId',
        'actions'   => array(
          array(
            'caption' => Mage::helper('productquestion')->__('Reply'),
            'url'     => array('base' => '*/*/edit'),
            'field'   => 'id'
          )
        ),
        'filter'    => FALSE,
        'sortable'  => FALSE,
        'index'     => 'stores',
        'is_system' => TRUE,
      ));

    $this->addExportType('*/*/exportCsv', Mage::helper('productquestion')->__('CSV'));
    $this->addExportType('*/*/exportXml', Mage::helper('productquestion')->__('XML'));

    return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('productquestion_id');
    $this->getMassactionBlock()->setFormFieldName('productquestion');

    $this->getMassactionBlock()->addItem('delete', array(
      'label'   => Mage::helper('productquestion')->__('Delete'),
      'url'     => $this->getUrl('*/*/massDelete'),
      'confirm' => Mage::helper('productquestion')->__('Are you sure?')
    ));

    $statuses = Mage::getSingleton('productquestion/status')->getOptionArray();
    $this->getMassactionBlock()->addItem('status', array(
      'label'      => Mage::helper('productquestion')->__('Change visibility status'),
      'url'        => $this->getUrl('*/*/massStatus', array('_current' => TRUE)),
      'additional' => array(
        'visibility' => array(
          'name'   => 'status',
          'type'   => 'select',
          'class'  => 'required-entry',
          'label'  => Mage::helper('productquestion')->__('Status'),
          'values' => $statuses
        )
      )
    ));
    return $this;
  }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}