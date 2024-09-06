<?php

namespace Pallapay\PPG\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Api\Data\CartInterface;

class PallapayMethod extends AbstractMethod
{
    const METHOD_CODE = 'pallapay_ppg';

    protected $_code = 'pallapay_ppg';

    public function isAvailable(CartInterface $quote = null) {
        $apiKey = $this->_scopeConfig->getValue('payment/pallapay_ppg/api_key', ScopeInterface::SCOPE_STORE);
        $secretKey = $this->_scopeConfig->getValue('payment/pallapay_ppg/secret_key', ScopeInterface::SCOPE_STORE);

        if (!$apiKey || !$secretKey) {
            return false;
        }

        return parent::isAvailable($quote);
    }
}
