<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Block_Adminhtml_Productquestion_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId = 'id';
    $this->_blockGroup = 'productquestion';
    $this->_controller = 'adminhtml_productquestion';

    $this->_updateButton('save', 'label', Mage::helper('productquestion')->__('Save'));
    $this->_updateButton('delete', 'label', Mage::helper('productquestion')->__('Delete'));

    $this->_addButton('saveandcontinue', array(
      'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
      'onclick' => 'saveAndContinueEdit()',
      'class'   => 'save',
    ), -100);

    $this->_formScripts[] = "
    function toggleEditor() {
    if (tinyMCE.getInstanceById('productquestion_content') == null) {
    tinyMCE.execCommand('mceAddControl', false, 'productquestion_content');
    } else {
    tinyMCE.execCommand('mceRemoveControl', false, 'productquestion_content');
    }
    }

    function saveAndContinueEdit(){
    editForm.submit($('edit_form').action+'back/edit/');
    }
    ";
  }

  public function getHeaderText()
  {
    if (Mage::registry('productquestion_data') && Mage::registry('productquestion_data')->getId()) {
      return Mage::helper('productquestion')->__("Reply question from %s (%s) ", $this->htmlEscape(Mage::registry('productquestion_data')->getData('author_name')), $this->htmlEscape(Mage::registry('productquestion_data')->getData('author_email')));
    } else {
      return Mage::helper('productquestion')->__('Add Question');
    }
  }
}