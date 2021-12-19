<?php

namespace Meetanshi\Knet\Controller\Order;

use Meetanshi\Knet\Controller\Main;

/**
 * Class Fail
 * @package Meetanshi\Knet\Controller\Payment
 */
class Success extends Main
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
