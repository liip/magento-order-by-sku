<?php

namespace Liip\OrderBySku\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class OrderBySku extends Template implements BlockInterface
{
    const SHOW_QTY_FIELD_NAME = 'show_qty';

    /**
     * @return bool
     */
    public function isQtyInputVisible()
    {
        return (bool)$this->getData(self::SHOW_QTY_FIELD_NAME);
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->_urlBuilder->getUrl('orderbysku/index/post');
    }
}
