<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Controller\Result;
use Magento\Framework\View\Result\PageFactory;
use Yosto\CustomerAttribute\Controller\Adminhtml\Attribute;
use Yosto\CustomerAttribute\Model\EavAttributeFactory;
use Yosto\CustomerAttribute\Model\ResourceModel\EavAttribute\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Serialize\Serializer\FormData;
/**
 * Save customera and address attribute
 *
 * Class Save
 * @package Yosto\CustomerAttribute\Controller\Adminhtml\Attribute
 */
class Save extends Attribute
{
    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var
     */
    protected $_attributeLabelCache;


    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
     */
    protected $groupCollectionFactory;


    /**
     * @var AttributeSetFactory
     */
    protected $_attributeSetFactory;


    /**
     * var AttributeRepository
     */
    protected $_attributeRepository;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var FormData|null
     */
    private $formDataSerializer;

    /**
     * @var
     */
    protected $_eavConfig;

    protected $_cache;

    protected $_cacheManager;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param AttributeFactory $attributeFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param EavAttributeFactory $eavAttributeFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param AttributeSetFactory $attributeSetFactory
     * @param AttributeRepository $attributeRepository
     * @param \Magento\Eav\Model\ConfigFactory $eavConfig
     * @param CacheManager $cacheManger
     * @param CacheTypeListInterface $cache
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        EavAttributeFactory $eavAttributeFactory,
        \Psr\Log\LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Filter $filter,
        AttributeSetFactory $attributeSetFactory,
        AttributeRepository $attributeRepository,
        \Magento\Eav\Model\ConfigFactory $eavConfig,
        CacheManager $cacheManger,
        CacheTypeListInterface $cache,
        LayoutFactory $layoutFactory,
        FormData $formData
    ) {
        parent::__construct(
            $context,
            $logger,
            $resultPageFactory,
            $collectionFactory,
            $eavAttributeFactory,
            $attributeFactory,
            $filter,
            $coreRegistry
        );
        $this->attributeFactory = $attributeFactory;
        $this->validatorFactory = $validatorFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->_attributeSetFactory = $attributeSetFactory;
        $this->_attributeRepository = $attributeRepository;
        $this->_eavConfig = $eavConfig;
        $this->productHelper = $productHelper;
        $this->_cache = $cache;
        $this->_cacheManager = $cacheManger;
        $this->formDataSerializer = $formData;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {

        try {
            $optionData = $this->formDataSerializer
                ->unserialize($this->getRequest()->getParam('serialized_options', '[]'));
        } catch (\InvalidArgumentException $e) {
            $message = __("The attribute couldn't be saved due to an error. Verify your information and try again. "
                . "If the error persists, please try again later.");
            $this->messageManager->addErrorMessage($message);
            return $this->returnResult('yosto_customer_attribute/*/edit', ['_current' => true], ['error' => true]);
        }

        $data = $this->getRequest()->getPostValue();
        $data = array_replace_recursive(
            $data,
            $optionData
        );
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {

            $redirectBack = $this->getRequest()->getParam('back', false);

            $model = $this->attributeFactory->create();
            $attributeId = $this->getRequest()->getParam('attribute_id');
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $frontendLabel = $this->getRequest()->getParam('frontend_label');

            /**
             * Auto gen code
             */
            $attributeCode = $attributeCode ?: $this->generateCode($frontendLabel[0]);

            /*
             * Validate attribute code
             */
            if (strlen($this->getRequest()->getParam('attribute_code')) > 0) {
                $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );
                    return $resultRedirect->setPath('yosto_customer_attribute/*/edit', ['attribute_id' => $attributeId, '_current' => true]);
                }
            }
            $data['attribute_code'] = $attributeCode;

            //validate frontend_input
            /** @var $inputType \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator */
//            if (isset($data['frontend_input'])) {
//                $inputType = $this->validatorFactory->create();
//                if (!$inputType->isValid($data['frontend_input'])) {
//                    foreach ($inputType->getMessages() as $message) {
//                        $this->messageManager->addErrorMessage($message);
//                    }
//                    return $resultRedirect->setPath('yosto_customer_attribute/*/edit', ['attribute_id' => $attributeId, '_current' => true]);
//                }
//            }


            if ($attributeId) {
                $model->load($attributeId);

                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                    return $resultRedirect->setPath('yosto_customer_attribute/*/');
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $this->messageManager->addErrorMessage(__('We can\'t update the attribute.'));
                    $this->_session->setAttributeData($data);
                    return $resultRedirect->setPath('yosto_customer_attribute/*/');
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();

            } else {
                /**
                 * @todo add to helper and specify all relations for properties
                 */
                $data['source_model'] = $this->productHelper->getAttributeSourceModelByInputType(
                    $data['frontend_input']
                );
                $data['backend_model'] = $this->productHelper->getAttributeBackendModelByInputType(
                    $data['frontend_input']
                );
            }


            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            $model->addData($data);

            try {

                $model->setEntityTypeId($this->_entityTypeId);
                /*if ($data['frontend_input'] == "multiselect") {
                    $model->setBackendModel('Yosto\CustomerAttribute\Model\Attribute\Backend\Multiselect');
                } elseif ($data['frontend_input'] == "file") {
                    $model->setBackendModel('Yosto\CustomerAttribute\Model\Attribute\Backend\File');
                } elseif ($data['frontend_input'] == "image") {
                    $model->setBackendModel('Yosto\CustomerAttribute\Model\Attribute\Backend\Image');
                }*/
                $model->save();

                $this->updateMetadataForAttribute($data, $model);
                $this->_cache->invalidate(['full_page', 'eav']);
                $this->messageManager->addSuccessMessage(__('You saved the customer attribute.'));

                $this->_session->setAttributeData(false);

                if ($redirectBack) {
                    $resultRedirect->setPath('yosto_customer_attribute/*/edit', ['attribute_id' => $model->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('yosto_customer_attribute/*/');
                }
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setAttributeData($data);
                return $resultRedirect->setPath('yosto_customer_attribute/*/edit', ['attribute_id' => $attributeId, '_current' => true]);
            }
        }
        return $resultRedirect->setPath('yosto_customer_attribute/*/');
    }

    /**
     * @param $data
     * @param  \Magento\Eav\Model\Entity\Attribute $model
     */
    public function updateMetadataForAttribute($data, $model)
    {
        /** @var \Yosto\CustomerAttribute\Helper\Data $configData */
        $configData = $this->_objectManager->create(\Yosto\CustomerAttribute\Helper\Data::class);

        $my_attribute = $this->_attributeRepository->get(
            'customer',
            $model->getAttributeCode()
        );

        // Check attribute_set & attribute_group for address
        $customerEntity = $this->_eavConfig->create()->getEntityType('customer');
        $customerSetId = $customerEntity->getDefaultAttributeSetId();
        $customerAttributeSet = $this->_attributeSetFactory->create();
        $customerAttributeGroupId = $customerAttributeSet->getDefaultGroupId($customerSetId);
        if ($configData->isMigratedSystem()) {
            $customerSetId = 1;
            $customerAttributeGroupId = 1;
        }
        /**
         * Set forms
         */
        $useInForms = [];
        if (key_exists('use_in_forms', $data) && is_array($data['use_in_forms'])) {
            $useInForms = $data['use_in_forms'];
        }

        $my_attribute->setData('attribute_id', $model->getAttributeId())
            ->setData("used_in_forms", $useInForms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", $data['is_visible'])
            ->setData('attribute_set_id', $customerSetId)
            ->setData('attribute_group_id', $customerAttributeGroupId);

        $my_attribute->save();
    }


    /**
     * Returns result of authorisation permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Yosto_CustomerAttribute::new_attribute');
    }


    /**
     * Provides an initialized Result object.
     *
     * @param string $path
     * @param array $params
     * @param array $response
     * @return Json|Redirect
     */
    private function returnResult($path = '', array $params = [], array $response = [])
    {
        if ($this->isAjax()) {
            $layout = $this->layoutFactory->create();
            $layout->initMessages();

            $response['messages'] = [$layout->getMessagesBlock()->getGroupedHtml()];
            $response['params'] = $params;
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($response);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath($path, $params);
    }
}