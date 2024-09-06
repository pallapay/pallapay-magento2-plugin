<?php

namespace Pallapay\PPG\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;

class Success extends Action
{
    protected $orderRepository;

    public function __construct(Context $context, OrderRepositoryInterface $orderRepository) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        $this->_redirect('checkout/onepage/success', ['_secure' => true]);
    }
}


