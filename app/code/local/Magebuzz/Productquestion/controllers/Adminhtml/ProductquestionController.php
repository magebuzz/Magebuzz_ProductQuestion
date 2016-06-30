<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com
 */

class Magebuzz_Productquestion_Adminhtml_ProductquestionController extends Mage_Adminhtml_Controller_action
{
  const XML_PATH_EMAIL_RECIPIENT = 'productquestion/email/send_email_to_admin';
  const XML_PATH_EMAIL_SENDER = 'productquestion/email/email_sender';
  const XML_PATH_EMAIL_TEMPLATE = 'productquestion/email/email_customer_template';

  protected function _initAction()
  {
    $this->loadLayout()
      ->_setActiveMenu('productquestion/items')
      ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
    return $this;
  }

  public function indexAction()
  {
    $this->_initAction()
      ->renderLayout();
  }

  public function editAction()
  {
    $id = $this->getRequest()->getParam('id');
    $model = Mage::getModel('productquestion/productquestion')->load($id);
    $product_data = $model->getData();
    $productName = '';
    if (isset($product_data['product_id'])) {
      $product = Mage::getModel('catalog/product')->load($product_data['product_id'])->getData();
      $productName = $product['name'];
    }
    $model->setData('product_name', $productName);
    if ($model->getId() || $id == 0) {
      $data = Mage::getSingleton('adminhtml/session')->getFormData(TRUE);
      if (!empty($data)) {
        $model->setData($data);
      }
      Mage::register('productquestion_data', $model);
      $this->loadLayout();
      $this->_setActiveMenu('productquestion/items');
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
      $this->getLayout()->getBlock('head')->setCanLoadExtJs(TRUE);
      $this->_addContent($this->getLayout()->createBlock('productquestion/adminhtml_productquestion_edit'));
      $this->_addLeft($this->getLayout()->createBlock('productquestion/adminhtml_productquestion_edit_tabs'));
      $this->renderLayout();
    } else {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productquestion')->__('Item does not exist'));
      $this->_redirect('*/*/');
    }
  }

  public function newAction()
  {
    $this->_forward('edit');
  }

  public function saveAction()
  {
    if ($data = $this->getRequest()->getPost()) {
      $model = Mage::getModel('productquestion/productquestion');
      $productModel = Mage::getModel('productquestion/productquestionproduct');
      $storeModel = Mage::getModel('productquestion/productquestionstore');
      $model->setData($data)
        ->setId($this->getRequest()->getParam('id'));
      try {
        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('productquestion/productquestion')->load($id);
        $replied_before = $collection->getData('replied');
        $model->setReplied(1);
        $productIds = array();
        if(isset($data['productIds'])) {
          $productIds = $data['productIds'];
        }
        if ($data['date'] == '') {
          $model->setDate(Mage::getModel('core/date')->gmtTimestamp());
        }
        $model->save();
        $productquestionId = $model->getProductquestionId();
        $productCollection = $productModel->getCollection()->AddFieldToFilter('productquestion_id', $productquestionId)->getData();
        $productIdsOld = array();
        foreach ($productCollection as $pro) {
          $productIdsOld[] = $pro['product_id'];
        }
        $model->compareProductList($productIds, $productIdsOld, $productquestionId);
        $storeModel->saveStoreView($data['stores'], $productquestionId);
        if ($replied_before == '0') {
          //send email to customer
          foreach ($productIdsOld as $product) {
            $_product = Mage::getModel('catalog/product')->load($product);
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(FALSE);
            $postObject = new Varien_Object();
            $postObject->setData($data);
            $product_url = '';
            $product_url .= '<a href="' . $_product->getProductUrl() . '">' . $_product->getProductUrl() . '</a>';
            $mailTemplate = Mage::getModel('core/email_template');
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
              ->setReplyTo(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT))
              ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                $data['author_email'],
                null,
                array('data'         => $postObject,
                      'product_name' => $_product->getName(),
                      'product_url'  => $product_url,
                ));
            $translate->setTranslateInline(TRUE);
          }
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productquestion')->__('Question was successfully replied'));
        } else {
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productquestion')->__('Question was successfully saved'));
        }
        Mage::getSingleton('adminhtml/session')->setFormData(FALSE);

        if ($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', array('id' => $model->getId()));
          return;
        }
        $this->_redirect('*/*/');
        return;
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productquestion')->__('Unable to find item to reply'));
    $this->_redirect('*/*/');
  }

  public function deleteAction()
  {
    if ($this->getRequest()->getParam('id') > 0) {
      try {
        $model = Mage::getModel('productquestion/productquestion');
        $model->setId($this->getRequest()->getParam('id'))
          ->delete();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
        $this->_redirect('*/*/');
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
      }
    }
    $this->_redirect('*/*/');
  }

  public function massDeleteAction()
  {
    $productquestionIds = $this->getRequest()->getParam('productquestion');
    if (!is_array($productquestionIds)) {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
    } else {
      try {
        foreach ($productquestionIds as $productquestionId) {
          $productquestion = Mage::getModel('productquestion/productquestion')->load($productquestionId);
          $productquestion->delete();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(
          Mage::helper('adminhtml')->__(
            'Total of %d record(s) were successfully deleted', count($productquestionIds)
          )
        );
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  public function massStatusAction()
  {
    $productquestionIds = $this->getRequest()->getParam('productquestion');
    if (!is_array($productquestionIds)) {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
    } else {
      try {
        foreach ($productquestionIds as $productquestionId) {
          $productquestion = Mage::getSingleton('productquestion/productquestion')
            ->load($productquestionId)
            ->setVisibility($this->getRequest()->getParam('status'))
            ->setIsMassupdate(TRUE)
            ->save();
        }
        $this->_getSession()->addSuccess(
          $this->__('Total of %d record(s) were successfully updated', count($productquestionIds))
        );
      } catch (Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  public function exportCsvAction()
  {
    $fileName = 'productquestion.csv';
    $content = $this->getLayout()->createBlock('productquestion/adminhtml_productquestion_grid')
      ->getCsv();
    //$this->_sendUploadResponse($fileName, $content);
    $this->_prepareDownloadResponse($fileName, $content);
  }

  public function exportXmlAction()
  {
    $fileName = 'productquestion.xml';
    $content = $this->getLayout()->createBlock('productquestion/adminhtml_productquestion_grid')
      ->getXml();
    //$this->_sendUploadResponse($fileName, $content);
    $this->_prepareDownloadResponse($fileName, $content);
  }

  protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
  {
    $response = $this->getResponse();
    $response->setHeader('HTTP/1.1 200 OK', '');
    $response->setHeader('Pragma', 'public', TRUE);
    $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', TRUE);
    $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
    $response->setHeader('Last-Modified', date('r'));
    $response->setHeader('Accept-Ranges', 'bytes');
    $response->setHeader('Content-Length', strlen($content));
    $response->setHeader('Content-type', $contentType);
    $response->setBody($content);
    $response->sendResponse();
  }
  
  public function productGridAction(){
    $this->loadLayout();
    $this->getResponse()->setBody(
	    $this->getLayout()->createBlock('productquestion/adminhtml_productquestion_edit_tab_product')->toHtml()
    );
  }
}