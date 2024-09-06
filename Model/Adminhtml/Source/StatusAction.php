<?php

namespace Pallapay\PPG\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection as MagentoCollection;

class StatusAction implements OptionSourceInterface
{
    protected $statusCollection;

    /**
     * @param MagentoCollection $statusCollectionFactory
     */
    public function __construct(MagentoCollection $statusCollectionFactory) {
        $this->statusCollection = $statusCollectionFactory;
    }

    /**
     * @return mixed
     */
    public function getorderstatusarray() {
        return $this->statusCollection->toOptionArray();
    }

    /**
     * @return mixed
     */
    public function toOptionArray() {
        return $this->getorderstatusarray();
    }
}
