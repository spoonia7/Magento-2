<?php

namespace Zkood\CouponsSelling\Controller\Block;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\LayoutFactory as LayoutResultFactory;

class Index implements HttpGetActionInterface
{
    /**
     * @var LayoutResultFactory
     */
    private $layoutFactory;

    public function __construct(LayoutResultFactory $layoutFactory)
    {
        $this->layoutFactory = $layoutFactory;
    }

    public function execute()
    {
        $result = $this->layoutFactory->create();
        $result->addHandle($_REQUEST['handle']);
        return $result;
    }
}
