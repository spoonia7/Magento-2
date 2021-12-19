<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\OrderAttribute\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Yosto\OrderAttribute\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'custom_attribute_shipping_address_data',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Custom shipping address data',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'custom_attribute_billing_address_data',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Custom billing address data',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_address'),
            'custom_attribute_shipping_address_data',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Custom shipping address data',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_address'),
            'custom_attribute_billing_address_data',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Custom billing address data',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'custom_attribute_shipping_address_data',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Custom billing address data',
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'custom_attribute_billing_address_data',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Custom billing address data',
            ]
        );
        $installer->endSetup();
    }

}