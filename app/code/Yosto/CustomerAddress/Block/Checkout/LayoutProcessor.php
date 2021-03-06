<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAddress\Block\Checkout;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Checkout\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class LayoutProcessor
 * @package Yosto\CustomerAddress\Block\Checkout
 */
class LayoutProcessor extends \Magento\Checkout\Block\Checkout\LayoutProcessor
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var \Magento\Customer\Model\Options
     */
    private $options;

    /**
     * @var Data
     */
    private $checkoutDataHelper;


    private $productMetadata;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    private $shippingConfig;

    /**
     * LayoutProcessor constructor.
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper $attributeMapper
     * @param AttributeMerger $merger
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Shipping\Model\Config|null $shippingConfig
     * @param StoreManagerInterface|null $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Shipping\Model\Config $shippingConfig = null,
        StoreManagerInterface $storeManager = null
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->merger = $merger;
        $this->productMetadata = $productMetadata;
        $this->shippingConfig = $shippingConfig ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Shipping\Model\Config::class);
        $this->storeManager = $storeManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
    }

    /**
     * @deprecated
     * @return \Magento\Customer\Model\Options
     */
    private function getOptions()
    {
        if (!is_object($this->options)) {
            $this->options = ObjectManager::getInstance()->get(\Magento\Customer\Model\Options::class);
        }
        return $this->options;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAddressAttributes()
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];
        /**
         * Additional metadata for attribute which core magento does not generate.
         */
        foreach ($attributes as $attr) {
            $elements[$attr->getAttributeCode()] = $this->attributeMapper->map($attr);
            $code = $attr->getData('attribute_code');
            $frontendClass = $attr->getFrontendClass();
            $isRequired = $attr->getData('is_required');
            $validation = [];
            if ($isRequired) {
                $validation["required-entry"] = true;
            }

            if ($frontendClass != null) {
                $validation[$frontendClass . ''] = true;
            }
            $elements["{$code}"]["validation"] = $validation;
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }


        }
        return $elements;
    }

    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary
     *
     * @param array $elements address attributes
     * @param array $attributesToConvert fields and their callbacks
     * @return array
     */
    private function convertElementsToSelect($elements, $attributesToConvert)
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($elements) as $code) {
            if (!in_array($code, $codes)) {
                continue;
            }
            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }
            $elements[$code]['dataType'] = 'select';
            $elements[$code]['formElement'] = 'select';

            foreach ($options as $key => $value) {
                $elements[$code]['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }

        return $elements;
    }

    /**
     * @param array $jsLayout
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($jsLayout)
    {
        $attributesToConvert = [
            'prefix' => [$this->getOptions(), 'getNamePrefixOptions'],
            'suffix' => [$this->getOptions(), 'getNameSuffixOptions'],
        ];

        $elements = $this->getAddressAttributes();
        $elements = $this->convertElementsToSelect($elements, $attributesToConvert);

        // The following code is a workaround for custom address attributes
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']
        )) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children'] = $this->processPaymentChildrenComponents(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children'],
                $elements
            );
        }
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['step-config']['children']['shipping-rates-validation']['children'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['step-config']['children']['shipping-rates-validation']['children'] =
                $this->processShippingChildrenComponents(
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['step-config']['children']['shipping-rates-validation']['children']
                );
        }

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
        )) {
            $fields = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'shippingAddress',
                $fields
            );
        }
        return $jsLayout;
    }

    /**
     * Process shipping configuration to exclude inactive carriers.
     *
     * @param array $shippingRatesLayout
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function processShippingChildrenComponents($shippingRatesLayout)
    {
        $activeCarriers = $this->shippingConfig->getActiveCarriers(
            $this->storeManager->getStore()->getId()
        );
        foreach (array_keys($shippingRatesLayout) as $carrierName) {
            $carrierKey = str_replace('-rates-validation', '', $carrierName);
            if (!array_key_exists($carrierKey, $activeCarriers)) {
                unset($shippingRatesLayout[$carrierName]);
            }
        }
        return $shippingRatesLayout;
    }

    /**
     * Appends billing address form component to payment layout
     *
     * @param array $paymentLayout
     * @param array $elements
     *
     * @return array
     */
    private function processPaymentChildrenComponents(
        array $paymentLayout,
        array $elements
    ) {
        if (!isset($paymentLayout['payments-list']['children'])) {
            $paymentLayout['payments-list']['children'] = [];
        }

        if (!isset($paymentLayout['afterMethods']['children'])) {
            $paymentLayout['afterMethods']['children'] = [];
        }

        // if billing address should be displayed on Payment method or page
        if ($this->getCheckoutDataHelper()->isDisplayBillingOnPaymentMethodAvailable()) {
            $paymentLayout['payments-list']['children'] = array_merge_recursive(
                $paymentLayout['payments-list']['children'],
                $this->processPaymentConfiguration(
                    $paymentLayout['renders']['children'],
                    $elements
                )
            );
        } else {
            $component['billing-address-form'] = $this->getBillingAddressComponent(
                'shared',
                $elements
            );

            $paymentLayout['afterMethods']['children'] = array_merge_recursive(
                $component,
                $paymentLayout['afterMethods']['children']
            );
        }

        return $paymentLayout;
    }

    /**
     * Inject billing address component into every payment component
     *
     * @param array $configuration list of payment components
     * @param array $elements attributes that must be displayed in address form
     * @return array
     */
    private function processPaymentConfiguration(array &$configuration, array $elements)
    {
        $output = [];
        foreach ($configuration as $paymentGroup => $groupConfig) {
            foreach ($groupConfig['methods'] as $paymentCode => $paymentComponent) {
                if (empty($paymentComponent['isBillingAddressRequired'])) {
                    continue;
                }

                $output[$paymentCode . '-form'] = $this->getBillingAddressComponent(
                    $paymentCode,
                    $elements
                );
            }
            unset($configuration[$paymentGroup]['methods']);
        }

        return $output;
    }

    /**
     * Gets billing address component details
     *
     * @param string $paymentCode
     * @param array  $elements
     *
     * @return array
     */
    private function getBillingAddressComponent($paymentCode, $elements)
    {


        /** @var \Magento\Framework\App\ProductMetadataInterface $productMetadata */
        $version = $this->productMetadata->getVersion();
        if (version_compare($version, '2.3.2') < 0) {
            return [
                'component' => 'Magento_Checkout/js/view/billing-address',
                'displayArea' => 'billing-address-form-' . $paymentCode,
                'provider' => 'checkoutProvider',
                'deps' => 'checkoutProvider',
                'dataScopePrefix' => 'billingAddress' . $paymentCode,
                'sortOrder' => 1,
                'children' => [
                    'form-fields' => [
                        'component' => 'uiComponent',
                        'displayArea' => 'additional-fieldsets',
                        'children' => $this->merger->merge(
                            $elements,
                            'checkoutProvider',
                            'billingAddress' . $paymentCode,
                            [
                                'country_id' => [
                                    'sortOrder' => 115,
                                ],
                                'region' => [
                                    'visible' => false,
                                ],
                                'region_id' => [
                                    'component' => 'Magento_Ui/js/form/element/region',
                                    'config' => [
                                        'template' => 'ui/form/field',
                                        'elementTmpl' => 'ui/form/element/select',
                                        'customEntry' => 'billingAddress' . $paymentCode . '.region',
                                    ],
                                    'validation' => [
                                        'required-entry' => true,
                                    ],
                                    'filterBy' => [
                                        'target' => '${ $.provider }:${ $.parentScope }.country_id',
                                        'field' => 'country_id',
                                    ],
                                ],
                                'postcode' => [
                                    'component' => 'Magento_Ui/js/form/element/post-code',
                                    'validation' => [
                                        'required-entry' => true,
                                    ],
                                ],
                                'company' => [
                                    'validation' => [
                                        'min_text_length' => 0,
                                    ],
                                ],
                                'fax' => [
                                    'validation' => [
                                        'min_text_length' => 0,
                                    ],
                                ],
                                'telephone' => [
                                    'config' => [
                                        'tooltip' => [
                                            'description' => __('For delivery questions.'),
                                        ],
                                    ],
                                ],
                            ]
                        ),
                    ],
                ],
            ];


        }

        return [
            'component' => 'Magento_Checkout/js/view/billing-address',
            'displayArea' => 'billing-address-form-' . $paymentCode,
            'provider' => 'checkoutProvider',
            'deps' => 'checkoutProvider',
            'dataScopePrefix' => 'billingAddress' . $paymentCode,
            'billingAddressListProvider' => '${$.name}.billingAddressList',
            'sortOrder' => 1,
            'children' => [
                'billingAddressList' => [
                    'component' => 'Magento_Checkout/js/view/billing-address/list',
                    'displayArea' => 'billing-address-list',
                    'template' => 'Magento_Checkout/billing-address/list'
                ],
                'form-fields' => [
                    'component' => 'uiComponent',
                    'displayArea' => 'additional-fieldsets',
                    'children' => $this->merger->merge(
                        $elements,
                        'checkoutProvider',
                        'billingAddress' . $paymentCode,
                        [
                            'country_id' => [
                                'sortOrder' => 115,
                            ],
                            'region' => [
                                'visible' => false,
                            ],
                            'region_id' => [
                                'component' => 'Magento_Ui/js/form/element/region',
                                'config' => [
                                    'template' => 'ui/form/field',
                                    'elementTmpl' => 'ui/form/element/select',
                                    'customEntry' => 'billingAddress' . $paymentCode . '.region',
                                ],
                                'validation' => [
                                    'required-entry' => true,
                                ],
                                'filterBy' => [
                                    'target' => '${ $.provider }:${ $.parentScope }.country_id',
                                    'field' => 'country_id',
                                ],
                            ],
                            'postcode' => [
                                'component' => 'Magento_Ui/js/form/element/post-code',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                            'company' => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'fax' => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'telephone' => [
                                'config' => [
                                    'tooltip' => [
                                        'description' => __('For delivery questions.'),
                                    ],
                                ],
                            ],
                        ]
                    ),
                ],
            ],
        ];
    }

    /**
     * Get checkout data helper instance
     *
     * @return Data
     * @deprecated
     */
    private function getCheckoutDataHelper()
    {
        if (!$this->checkoutDataHelper) {
            $this->checkoutDataHelper =
                ObjectManager::getInstance()->get(Data::class);
        }

        return $this->checkoutDataHelper;
    }
}