<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Yosto\CustomerAttribute\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'custom_attribute_shipping_address_data',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Custom attribute shipping address data',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'custom_attribute_billing_address_data',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Custom attribute billing address data',
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.3.0') < 0) {
            if (!$installer->getConnection()->isTableExists($installer->getTable('yosto_customer_attribute_relation'))) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('yosto_customer_attribute_relation'));

                $table->addColumn(
                    'relation_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'unsigned' => true,
                        'primary' => true
                    ],
                    'Attribute relation table primary key'
                )
                    ->addColumn(
                        'relation_name',
                        Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => false
                        ]
                    )
                    ->addColumn(
                        'parent_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Parent attribute id'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_BOOLEAN,
                        null,
                        [
                            'nullable' => false
                        ]
                    )
                    ->setComment('Attribute relation table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($table);
            }
            if (!$installer->getConnection()->isTableExists($installer->getTable('yosto_customer_attribute_relation_value'))) {
                $table = $installer->getConnection()->newTable($installer->getTable('yosto_customer_attribute_relation_value'));
                $table->addColumn(
                    'value_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'unsigned' => true,
                        'primary' => true
                    ],
                    'Value primary key'

                )
                    ->addColumn(
                        'relation_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Relation id reference from relation table'
                    )
                    ->addColumn(
                        'parent_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Parent attribute id'
                    )->addColumn(
                        'child_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Child attribute id'
                    )->addColumn(
                        'condition_value',
                        Table::TYPE_INTEGER,
                        'null',
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Condition value'
                    )->addForeignKey(
                        $installer->getFkName('yosto_customer_attribute_relation_value', 'relation_id', 'yosto_customer_attribute_relation', 'relation_id'),
                        'relation_id',
                        $installer->getTable('yosto_customer_attribute_relation'),
                        'relation_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Attribute relation value table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($table);
            }
        }
        $installer->endSetup();
    }

}