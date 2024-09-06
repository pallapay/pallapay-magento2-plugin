<?php

namespace Pallapay\PPG\Model\Api;

use Pallapay\PPG\Api\HandleWebhookInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Api\Data\OrderInterface;

class HandleWebhook implements HandleWebhookInterface
{
    protected $_request;

    private $_orderRepository;
    private $_scopeConfig;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Request $request
     */
    public function __construct(OrderRepositoryInterface $orderRepository, ScopeConfigInterface $scopeConfig, Request $request) {
        $this->_orderRepository = $orderRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->_request = $request;
    }

    /**
     * @return string
     */
    public function handle()
    {
        try {
            $request = file_get_contents('php://input');
            $body = json_decode($request, true);

            if (!$this->isValidWebhookCall($body)) {
                return 'Invalid webhook call';
            }

            $order_id = str_replace('magento:order:id:', "", $body['data']['order_id']);
            /** @var OrderInterface $order */
            $order = $this->_orderRepository->get($order_id);

            if ($body['data']['status'] == 'PAID') {
                $order->setStatus(
                    $this->_scopeConfig->getValue('payment/pallapay_ppg/completed_status', ScopeInterface::SCOPE_STORE)
                )->save();
            } elseif ($body['data']['status'] == 'UNPAID') {
                $order->setStatus(
                    $this->_scopeConfig->getValue('payment/pallapay_ppg/failed_status', ScopeInterface::SCOPE_STORE)
                )->save();
                $order->save();
            } else {
                return 'Invalid status';
            }

            return 'Successful webhook call';
        } catch (\Exception $e) {
            return 'Webhook error';
        }
    }

    /**
     * @param $body
     * @return bool
     */
    private function isValidWebhookCall($body): bool
    {
        if (!isset($body['data']) || !isset($body['approval_hash']) || !is_array($body['data'])) {
            return false;
        }

        $data = $body['data'];
        $requestApprovalHash = $body['approval_hash'];
        $secretKey = $this->_scopeConfig->getValue('payment/pallapay_ppg/secret_key', ScopeInterface::SCOPE_STORE);
        ksort($data);

        $approvalString = implode('', $data);
        $approvalHash = hash_hmac('sha256', $approvalString, $secretKey);

        return strtolower($approvalHash) == $requestApprovalHash;
    }
}
