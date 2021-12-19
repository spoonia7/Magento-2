<?php
namespace Pentaherb\ProductDetail\Block;

class CurrentCategory extends \Magento\Framework\View\Element\Template
{

    protected $_registry;
    protected $_categoryFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_categoryFactory = $categoryFactory;
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getlayerCurrentCategory()
    {
        $product = $this->_registry->registry('current_product');
        $categories = $product->getCategoryIds();
        return $this->_categoryFactory->create()->load(end($categories));
    }

}