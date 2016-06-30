<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com
 */
class Magebuzz_Productquestion_IndexController extends Mage_Core_Controller_Front_Action {
  const XML_PATH_EMAIL_RECIPIENT = 'productquestion/email/send_email_to_admin';
  const XML_PATH_EMAIL_SENDER = 'productquestion/email/email_sender';
  const XML_PATH_EMAIL_TEMPLATE = 'productquestion/email/email_admin_template';
  const XML_PATH_EMAIL_CONFIRMATION_TEMPLATE = 'productquestion/email/email_confirmation';
  const XML_PATH_PRIVATE_KEY = 'productquestion/general/private_key';

  public function indexAction() {
    if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
      if (Mage::getStoreConfig('productquestion/general/allow_guest_ask_question', Mage::app()->getStore()) == "0") {
        Mage::getSingleton('customer/session')->authenticate($this);
      }
    }
    $product = $this->_initProduct();

		if (!$product) {
			Mage::app()->getResponse()->setRedirect(Mage::getUrl());
			return false;
		}

    $this->loadLayout();
    if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
      $breadcrumbsBlock->addCrumb('product', array(
        'label'    => $product->getName(),
        'link'     => $product->getProductUrl(),
        'readonly' => TRUE,
      ));
      $breadcrumbsBlock->addCrumb('productquestion', array('label' => Mage::helper('productquestion')->__('Product Questions')));
    }
    $this->_initLayoutMessages('productquestion/session');
    $this->renderLayout();
  }

  protected function _initProduct() {
    Mage::dispatchEvent('review_controller_product_init_before', array('controller_action' => $this));
    $categoryId = (int)$this->getRequest()->getParam('category', FALSE);
    $productId = (int)$this->getRequest()->getParam('id');

    $product = $this->_loadProduct($productId);

    if ($categoryId) {
      $category = Mage::getModel('catalog/category')->load($categoryId);
      Mage::register('current_category', $category);
    }

    try {
      Mage::dispatchEvent('review_controller_product_init', array('product' => $product));
      Mage::dispatchEvent('review_controller_product_init_after', array('product' => $product, 'controller_action' => $this));
    } catch (Mage_Core_Exception $e) {
      Mage::logException($e);
      return FALSE;
    }

    return $product;
  }

  protected function _loadProduct($productId) {
    if (!$productId) {
      return FALSE;
    }

    $product = Mage::getModel('catalog/product')
      ->setStoreId(Mage::app()->getStore()->getId())
      ->load($productId);
    /**
     * @var $product Mage_Catalog_Model_Product
     */
    if (!$product->getId() || !$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
      return FALSE;
    }

    Mage::register('current_product', $product);
    Mage::register('product', $product);

    return $product;
  }

  protected function _save() {
    $post = $this->getRequest()->getPost();
    $_product = Mage::getModel('catalog/product')->load($post['product_id']);
    $storeId = Mage::app()->getStore()->getId();
    if ($post) {
      try {
        $model = Mage::getModel('productquestion/productquestion');
        $modelProduct = Mage::getModel('productquestion/productquestionproduct');
        $storeModel = Mage::getModel('productquestion/productquestionstore');
        //save question
        $model->setData($post);
        $now = Mage::getModel('core/date')->gmtTimestamp();
        $model->setDate(date('Y-m-d H:i:s', $now));
        $model->save();
        //save product
        $productQuestionId = $model->getProductquestionId();
        $modelProduct->setProductquestionId($productQuestionId);
        $modelProduct->setProductId($post['product_id']);
        $modelProduct->save();
        //save store
        $dataStore = array('productquestion_id' => $productQuestionId, 'store_id' => $storeId);
        $storeModel->saveStoreId($dataStore);
        //send mail
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(FALSE);
        $postObject = new Varien_Object();
        $postObject->setData($post);
        $productUrl = '';
        $productUrl .= '<a href="' . $_product->getProductUrl() . '">' . $_product->getProductUrl() . '</a>';
        $mailTemplateAdmin = Mage::getModel('core/email_template');
        $mailTemplateAdmin->setDesignConfig(array('area' => 'frontend'))
          ->setReplyTo($post['author_email'])
          ->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
            Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
            null,
            array('data'         => $postObject,
                  'product_name' => $_product->getName(),
                  'product_url'  => $productUrl,
            ));

        if (Mage::getStoreConfig('productquestion/general/enable_email_confirmation')) {
            $replyto = Mage::getStoreConfig('productquestion/email/send_email_to_admin');
            $mailTemplateCustomer = Mage::getModel('core/email_template');
            $mailTemplateCustomer->setDesignConfig(array('area' => 'frontend'))
              ->setReplyTo($replyto)
              ->sendTransactional(
              Mage::getStoreConfig(self::XML_PATH_EMAIL_CONFIRMATION_TEMPLATE),
              Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
              $post['author_email'],
              null,
              array('data'         => $postObject,
                    'product_name' => $_product->getName(),
                    'product_url'  => $productUrl,
              ));
        }

        $translate->setTranslateInline(TRUE);
        Mage::getSingleton('core/session')->addSuccess('Your question has been submitted!');
        Mage::getSingleton('productquestion/session')->setFormData(FALSE);
        return;
      } catch (Exception $e) {
        Mage::getSingleton('core/session')->addError('An error has occured. Please try again later!');
        Mage::app()->getResponse()->setRedirect($post['current_url']);
        return;
      }
    } else {
      Mage::getSingleton('core/session')->addError('Please try again later!');
      Mage::app()->getResponse()->setRedirect($post['current_url']);
      return;
    }
  }

  public function postAction() {
    $post = $this->getRequest()->getPost();
    if (Mage::getStoreConfig('productquestion/general/enable_question_captcha', Mage::app()->getStore()) == "1") {
      if (!isset($post['g-recaptcha-response'])) {
        $this->_redirect('*/');
        return;
      }

      require_once(Mage::getBaseDir('lib') . DS . 'reCaptcha' . DS . 'recaptchalib.php');
      $privatekey = Mage::getStoreConfig(self::XML_PATH_PRIVATE_KEY);

      $resp = null;
      $error = null;

      $reCaptcha = new ReCaptcha($privatekey);
      $remote_addr = $this->getRequest()->getServer('REMOTE_ADDR');

      $resp = $reCaptcha->verifyResponse(
          $remote_addr,
          $post['g-recaptcha-response']
      );

      if ($resp->errorCodes == "missing-input") {
        Mage::getSingleton('core/session')->addError('Please check the reCaptcha');
        $this->_redirectReferer();
        return;
      }

      if ($resp != null && $resp->success) {
        $this->_save();
        $this->_redirectReferer();
        return;
      } else {
        Mage::getSingleton('core/session')->addError('An error has occured because of reCaptcha. Please try again later!');
        Mage::getSingleton('productquestion/session')->setFormData($post);
        $this->_redirectReferer();
        return;
      }
    } else {
      $this->_save();
      $this->_redirectReferer();
      return;
    }
  }

  public function voteAction() {
    $params = $this->getRequest()->getParams();
    if ($params) {
      $requestModel = Mage::getModel('productquestion/productquestion');
      $request = $requestModel->load($params['requestid']);
      $helper = Mage::helper('productquestion');

      try {
        if ($params['vote'] == 'voteup') {
          $vote = $request->getCountup() + 1;
          $request->setCountup($vote);
          $request->save();
        } else if ($params['vote'] == 'votedown') {
          $vote = $request->getCountdown() + 1;
          $request->setCountdown($vote);
          $request->save();
        }
      } catch (Exception $e) {
        Mage::log($e->getMessage());
        $this->_redirectReferer();
        return;
      }

      $requestCookieName = 'request_' . $params['requestid'] . '_' . $request->getProductId();
      $requestCookieValue = 'request_' . $params['requestid'] . '_' . $request->getProductId();
      $period = 31536000;
      Mage::getModel('core/cookie')->set($requestCookieName, $requestCookieValue, $period);
      $this->_redirectReferer();
      return;
    } else {
      $this->_redirectReferer();
      return;
    }
  }
}
