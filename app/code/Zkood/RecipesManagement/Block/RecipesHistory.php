<?php

namespace Zkood\RecipesManagement\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Zkood\RecipesManagement\Model\ResourceModel\Recipe\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class RecipesHistory extends Template
{
    /**
     * @var \Zkood\RecipesManagement\Model\ResourceModel\Recipe\CollectionFactory
     */
    protected $recipesCollectionFactory;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        CollectionFactory $recipesCollectionFactory,
        StoreManagerInterface $storeManager,
        SessionFactory $customerSession,
        Template\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->recipesCollectionFactory = $recipesCollectionFactory;
        $this->customerSession = $customerSession;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    public function isScopePrivate()
    {
        return true;
    }

    public function getRecipes()
    {
        $customer = $this->customerSession->create();
        $recipesCollection = $this->recipesCollectionFactory->create();
        return $recipesCollection->addFieldToFilter('customer_id', $customer->getCustomer()->getId())->load();
    }

    public function getActionUrl(): string
    {
        return $this->getBaseUrl() . 'customer/recipes/post';
    }

    public function getCustomer()
    {
        return $this->customerSession->create()->getCustomer();
    }

    public function getRecipeImage($imageName)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . "zkood/recipes" . $imageName;
    }

    public function getModalLink()
    {
        return $this->customerSession->create()->isLoggedIn() ? 'javascript:void(0)' : $this->getBaseUrl() . 'customer/account/login';
    }
}
