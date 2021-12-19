<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulCore
 * @author    Webkul <support@webkul.com>
 * @copyright 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulCore\Controller\Adminhtml\Carousel;

/**
 * Class ProductGridData for Carousel
 */
class SellerGridData extends \Magento\Backend\App\Action
{
    protected $resultLayoutFactory;
    protected $formKey;
    protected $request;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->formKey = $formKey;
        $this->request = $request;
        $this->request->setParam('form_key', $this->formKey->getFormKey());
    }

    /**
     * Execute Fucntion for Class ProductGridData
     *
     * @return jSon
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $this->getResponse()->setBody(
            $resultLayout->getLayout()->createBlock(
                "Webkul\MobikulCore\Block\Adminhtml\Edit\Carousel\Tab\Carouselsellers"
            )->toHtml()
        );
        return $resultLayout;
    }
}
