<?php

namespace Zkood\RecipesManagement\Controller\Recipes;

use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;

class Index extends AbstractAccount
{

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        PageFactory $resultPageFactory,
        Context $context
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Recipes History'));
        return $resultPage;
    }
}
