<?php

namespace Pallapay\PPG\Model\Payment;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CustomPaymentConfigProvider implements ConfigProviderInterface
{
    protected $methodCodes = [
        PallapayMethod::METHOD_CODE
    ];
    protected $methods = [];
    protected $escaper;

    private $scopeConfig;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(PaymentHelper $paymentHelper, Escaper $escaper, ScopeConfigInterface $scopeConfig) {
        $this->escaper = $escaper;
        $this->scopeConfig = $scopeConfig;

        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['pallapay_message'][$code] = $this->getPallapayMessage();
            }
        }
        return $config;
    }

    /**
     * @return false|string
     */
    private function getPallapayMessage()
    {
        $message = $this->scopeConfig->getValue('payment/pallapay_ppg/default_checkout_message', ScopeInterface::SCOPE_STORE);
        if ($message == NULL) {
            return false;
        }

        return '<div>' . nl2br($this->escaper->escapeHtml($message)) . '</div>';
    }
}
