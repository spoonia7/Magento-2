<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\AttributeRelation\Controller\Adminhtml;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute\CollectionFactory as EavCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Yosto\CustomerAttribute\Model\EavAttributeFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Yosto\AttributeRelation\Model\RelationFactory;
use Yosto\AttributeRelation\Model\RelationValueFactory;

abstract class Relation extends Action
{
    protected $_logger;
    protected $_resultPageFactory;
    protected $_eavAttributeFactory;
    protected $_coreRegistry;
    protected $_eavCollectionFactory;
    protected $_filter;
    protected $_relationFactory;
    protected $_relationValueFactory;
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        PageFactory $pageFactory,
        EavCollectionFactory $eavCollectionFactory,
        EavAttributeFactory $eavAttributeFactory,
        RelationFactory $relationFactory,
        RelationValueFactory $relationValueFactory,
        Filter $filter,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_resultPageFactory = $pageFactory;
        $this->_eavCollectionFactory = $eavCollectionFactory;
        $this->_relationFactory = $relationFactory;
        $this->_filter = $filter;
        $this->_eavAttributeFactory = $eavAttributeFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_relationValueFactory = $relationValueFactory;
    }

    /**
     * Returns result of authorisation permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::view_relations');
    }
}