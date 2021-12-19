<?php

namespace Zkood\CouponsSelling\Setup;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\GroupFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, GroupFactory $groupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->groupFactory = $groupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Customer::ENTITY,
            'seller_code',
            [
                'type' => 'varchar',
                'label' => 'Seller Code',
                'input' => 'text',
                'required' => false,
                'visible' => false,
                'user_defined' => false,
                'position' => 999,
                'system' => 0,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'seller',
            [
                'type' => 'int',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'label' => 'Seller',
                'input' => 'select',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'group' => 'General'
            ]
        );

        $groupFactory = $this->groupFactory->create();
        try {
            $groupFactory->setCode('Seller')->setTaxClassId(3)->save();
        } catch (\Exception $e) {
        }
    }
}
