<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\AddressAttribute\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Yosto\AddressAttribute\Model\ResourceModel\EavAttribute\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Yosto\AddressAttribute\Model\EavAttributeFactory;
/**
 * Class AbstractController
 * @package Yosto\AddressAttribute\Controller\Adminhtml
 */
abstract class AbstractController extends Action
{

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    protected $_entityTypeId;

    protected $_filter;

    protected $_eavAttributeFactory;

    protected $_coreRegistry;

    protected $_attributeFactory;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param PageFactory $pageFactory
     * @param CollectionFactory $collectionFactory
     * @param EavAttributeFactory $eavAttributeFactory
     * @param Filter $filter
     * @param Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        PageFactory $pageFactory,
        CollectionFactory $collectionFactory,
        EavAttributeFactory $eavAttributeFactory,
        AttributeFactory $attributeFactory,
        Filter $filter,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_resultPageFactory = $pageFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_filter = $filter;
        $this->_eavAttributeFactory = $eavAttributeFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->setEntityTypeId();
    }



    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    protected function generateCode($label)
    {
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }


    /**
     * Returns result of authorisation permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::view_address_attributes');
    }

    public function setEntityTypeId()
    {
        $this->_entityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            "customer_address"
        )->getTypeId();
    }

}