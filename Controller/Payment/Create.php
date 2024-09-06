<?php

namespace Pallapay\PPG\Controller\Payment;

use Exception;
use Magento\Framework\App\ActionInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Create implements ActionInterface
{
    private $checkoutSession;
    private $resultJsonFactory;
    private $scopeConfig;

    protected $urlBuilder;
    protected $apiKey;
    protected $secretKey;

    /**
     * @param Session $checkoutSession
     * @param JsonFactory $resultJsonFactory
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Session $checkoutSession, JsonFactory $resultJsonFactory, UrlInterface $urlBuilder,
                                ScopeConfigInterface $scopeConfig) {
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->apiKey = $this->scopeConfig->getValue('payment/pallapay_ppg/api_key', ScopeInterface::SCOPE_STORE);
        $this->secretKey = $this->scopeConfig->getValue('payment/pallapay_ppg/secret_key', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function execute()
    {
        /** @var Order $order */
        $order = $this->checkoutSession->getLastRealOrder();
        $pendingStatus = $this->scopeConfig->getValue('payment/pallapay_ppg/pending_status', ScopeInterface::SCOPE_STORE);
        $order->setStatus($pendingStatus)->save();

        $result = $this->resultJsonFactory->create();
        return $result->setData(['redirectUrl' => $this->getRedirectUrl($order)]);
    }

    /**
     * @param Order $order
     * @return string
     * @throws Exception
     */
    private function getRedirectUrl(Order $order): string {
        $customOrderId = $order->getId();
        $apiKey = $this->apiKey;
        $secretKey = $this->secretKey;
        $orderPaymentAmount = $order->getGrandTotal();
        $orderPaymentCurrency = $order->getOrderCurrencyCode();

        $timestamp = time();
        $approvalString = 'POST' . '/api/v1/api/payments' . $timestamp;
        $signature = hash_hmac('sha256', $approvalString, $secretKey);

        $params = json_encode([
            'symbol'              => $orderPaymentCurrency,
            'amount'              => strval($orderPaymentAmount),
            'webhook_url'         => $this->urlBuilder->getBaseUrl() . "rest/all/V1/order/update-status",
            'ipn_success_url'     => $this->urlBuilder->getUrl('pallapay/payment/success', ['_query' => ['order_id' => $order->getId()]]),
            'ipn_failed_url'      => $this->urlBuilder->getUrl('pallapay/payment/failed', ['_query' => ['order_id' => $order->getId()]]),
            'payer_email_address' => $order->getCustomerEmail(),
            'payer_first_name'    => $order->getCustomerFirstname(),
            'payer_last_name'     => $order->getCustomerLastname(),
            'order_id'            => 'magento:order:id:' . $customOrderId,
        ]);

        $curlCli = curl_init('https://app.pallapay.com/api/v1/api/payments');
        curl_setopt($curlCli, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Palla-Api-Key: ' . $apiKey,
            'X-Palla-Sign: ' . $signature,
            'X-Palla-Timestamp: ' . $timestamp,
        ]);
        curl_setopt($curlCli, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curlCli, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curlCli, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curlCli, CURLOPT_POST, TRUE);
        curl_setopt($curlCli, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($curlCli);
        curl_close($curlCli);
        $resultData = json_decode($result, TRUE);

        if (isset($resultData['is_successful']) && isset($resultData['data']) && isset($resultData['data']['payment_link']) && $resultData['is_successful']) {
            return $resultData['data']['payment_link'];
        } else {
            throw new Exception( json_encode($resultData) );
        }
    }
}
