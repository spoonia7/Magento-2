<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

/**
 * Product attribute add/edit form options tab
 *
 * @method \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Options setReadOnly(bool $value)
 * @method null|bool getReadOnly()
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Tab;

class Options extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\AbstractOptions
{
    /**
     * Preparing layout, adding buttons
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild('labels', \Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Options\Labels::class);
        $this->addChild('options', \Yosto\CustomerAttribute\Block\Adminhtml\Attribute\Edit\Options\Options::class);
        return parent::_prepareLayout();
    }
}
