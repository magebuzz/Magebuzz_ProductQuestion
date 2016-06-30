<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com
 */

class Magebuzz_Productquestion_Block_Adminhtml_Productquestion_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset('productquestion_form', array('legend' => Mage::helper('productquestion')->__('Question details')));
    $id = $this->getRequest()->getParam('id');
    $storeList = Mage::getSingleton('productquestion/productquestionstore')->getCollection()->AddFieldToFilter('productquestion_id', $id)->getData();
    $stores = array();
    if ($storeList) {
      foreach ($storeList as $store) {
        $stores[] = $store['store_id'];
      }
    }

    $fieldset->addField('date', 'date', array(
      'label'  => Mage::helper('productquestion')->__('Asked on'),
      'name'   => 'date',
      'image'  => $this->getSkinUrl('images/grid-cal.gif'),
      'format' => 'dd-MM-yyyy',
    ));

    $fieldset->addField('author_name', 'text', array(
      'label'    => Mage::helper('productquestion')->__('Author Name'),
      'class'    => 'required-entry',
      'required' => TRUE,
      'name'     => 'author_name',
    ));

    $fieldset->addField('author_email', 'text', array(
      'label'    => Mage::helper('productquestion')->__('Author Email'),
      'class'    => 'required-entry',
      'required' => TRUE,
      'name'     => 'author_email',
    ));

    $fieldset->addField('question', 'editor', array(
      'name'     => 'question',
      'label'    => Mage::helper('productquestion')->__('Question'),
      'title'    => Mage::helper('productquestion')->__('Question'),
      'required' => TRUE,
    ));

    $field = $fieldset->addField('store_id', 'multiselect', array(
      'name'     => 'stores[]',
      'label'    => Mage::helper('productquestion')->__('Store View'),
      'title'    => Mage::helper('productquestion')->__('Store View'),
      'required' => TRUE,
      'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(FALSE, TRUE),
    ));
    $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
    $field->setRenderer($renderer);

    $fieldset->addField('visibility', 'select', array(
      'label'  => Mage::helper('productquestion')->__('Visibility'),
      'name'   => 'visibility',
      'values' => array(
        array(
          'value' => 1,
          'label' => Mage::helper('productquestion')->__('Public'),
        ),

        array(
          'value' => 0,
          'label' => Mage::helper('productquestion')->__('Private'),
        ),
      ),
    ));

    $fieldset->addField('answer', 'editor', array(
      'name'     => 'answer',
      'label'    => Mage::helper('productquestion')->__('Answer'),
      'title'    => Mage::helper('productquestion')->__('Answer'),
      'required' => TRUE,
    ));

    if (Mage::getSingleton('adminhtml/session')->getProductquestionData()) {
      $form->setValues(Mage::getSingleton('adminhtml/session')->getProductquestionData());
      Mage::getSingleton('adminhtml/session')->setProductquestionData(null);
    } elseif (Mage::registry('productquestion_data')) {
      Mage::registry('productquestion_data')->setStoreId($stores);
      $form->setValues(Mage::registry('productquestion_data')->getData());
    }
    return parent::_prepareForm();
  }
}
