<?php
/**
 * Product attribute add/edit form options tab
 *
 * @method \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Options setReadOnly(bool $value)
 * @method null|bool getReadOnly()
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Tab;

class Options extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Preparing layout, adding buttons
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild('labels', \Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Options\Labels::class);
        $this->addChild('options', \Yosto\AddressAttribute\Block\Adminhtml\Attribute\Edit\Options\Options::class);
        return parent::_prepareLayout();
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml();
    }
}