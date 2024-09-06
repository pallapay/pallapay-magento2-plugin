<?php
namespace Pallapay\PPG\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Failed extends Action {
    private $checkoutSession;
    protected $orderRepository;
    private $scopeConfig;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Context $context, Session $checkoutSession, OrderRepositoryInterface $orderRepository,
                                ScopeConfigInterface $scopeConfig) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $orderId = $this->_request->getParam('order_id');
        /** @var OrderInterface $order */
        $order = $this->orderRepository->get($orderId);

        if ($order->getId() && !$order->isCanceled()) {
            $failedStatus = $this->scopeConfig->getValue('payment/pallapay_ppg/failed_status', ScopeInterface::SCOPE_STORE);
            $order->setStatus($failedStatus)->save();
        }

        $this->checkoutSession->restoreQuote();
        $this->_redirect('checkout/cart');
    }
}
