<?php

namespace Pallapay\PPG\Model\Adminhtml\Source;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field as MagentoField;

class DisabledField extends MagentoField
{
    protected $urlBuilder;

    /**
     * @param Context $context
     * @param array $data
     * @param UrlInterface $urlBuilder
     */
    public function __construct(Context $context, UrlInterface $urlBuilder, array $data = []) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param AbstractElement $element
     * @return mixed
     */
    protected function _getElementHtml(AbstractElement $element) {
        $element->setDisabled('disabled');
        $element->setValue($this->urlBuilder->getBaseUrl() . "rest/all/V1/order/update-status");
        return $element->getElementHtml();
    }
}
