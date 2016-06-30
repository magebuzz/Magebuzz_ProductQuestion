<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com 
 */

class Magebuzz_Productquestion_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function isEnabled()
  {
    return Mage::getStoreConfig('productquestion/general/enabled');
  }

  public function getPercentVote($requestId)
  {
    $requestModel = Mage::getSingleton('productquestion/productquestion')->load($requestId);
    $voteup = $requestModel->getCountup();
    $votedown = $requestModel->getCountdown();

    if ($voteup == 0 && $votedown == 0) {
      return 0;
    } else {

      return $voteup / ($voteup + $votedown) * 100;
    }
  }

  public function getCountVote($requestId)
  {
    $requestModel = Mage::getSingleton('productquestion/productquestion')->load($requestId);
    $voteup = $requestModel->getCountup();
    $votedown = $requestModel->getCountdown();
    return $votedown + $voteup;
  }

  public function getCaptchaLanguage()
  {
    return Mage::getStoreConfig('productquestion/general/lang', Mage::app()->getStore());;
  }

  public function getCaptchaTheme()
  {
    return Mage::getStoreConfig('productquestion/general/theme',Mage::app()->getStore());
  }

  public function getPublicKey()
  {
    return Mage::getStoreConfig('productquestion/general/public_key');
  }

  public function isCaptchaEnabled()
  {
    return Mage::getStoreConfig('productquestion/general/enable_question_captcha', Mage::app()->getStore());
  }
}