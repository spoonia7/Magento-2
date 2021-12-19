<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Yosto\CustomerAttribute\Model\EavAttributeFactory;
use Magento\Customer\Setup\CustomerSetupFactory;

/**
 * Class Attribute
 * @package Yosto\CustomerAttribute\Controller\Adminhtml
 */
abstract class Attribute extends Action
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
        $this->_coreRegistry = $coreRegistry;
        $this->_attributeFactory = $attributeFactory;
        $this->setAttributeTypeId();
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
            ->isAllowed('Yosto_CustomerAttribute::view_attributes');
    }
    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {

        return parent::dispatch($request);
    }

    public function setAttributeTypeId()
    {
        $this->_entityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            \Magento\Customer\Model\Customer::ENTITY
        )->getTypeId();
    }
}