<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_RewardPointsUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\RewardPointsUltimate\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\RewardPointsUltimate\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const MAGEPLAZA_REWARD_MILESTONE = 'mageplaza_reward_milestone';
    const MAGEPLAZA_REWARD_MILESTONE_CUSTOMER = 'mageplaza_reward_milestone_customer';

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $connection->addColumn(
                $setup->getTable('mageplaza_reward_behavior'),
                'min_days',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Min Days',
                    'after' => 'min_words'
                ]
            );
            $connection->addColumn(
                $setup->getTable('mageplaza_reward_behavior'),
                'is_loop',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'default' => 1,
                    'comment' => 'Is Comeback Login Loop',
                    'after' => 'is_purchased'
                ]
            );
            $connection->addColumn(
                $setup->getTable('mageplaza_reward_customer'),
                'is_comeback',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Is Comeback Login',
                    'after' => 'is_active'
                ]
            );

            /**
             * Mageplaza Reward milestone
             */
            if (!$installer->tableExists(self::MAGEPLAZA_REWARD_MILESTONE)) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable(self::MAGEPLAZA_REWARD_MILESTONE))
                    ->addColumn('tier_id', Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ], 'Rule Id')
                    ->addColumn('name', Table::TYPE_TEXT, 255, [], 'Name')
                    ->addColumn('status', Table::TYPE_SMALLINT, null, [
                        'nullable' => false,
                        'default' => '0',
                    ], 'Status')
                    ->addColumn('image', Table::TYPE_TEXT, 255, [], 'Tier Image')
                    ->addColumn(
                        'customer_group_ids',
                        Table::TYPE_TEXT,
                        255,
                        ['unsigned' => true, 'nullable' => false],
                        'Customer Group Ids'
                    )
                    ->addColumn('website_ids', Table::TYPE_TEXT, 255, ['nullable' => false], 'Website Ids')
                    ->addColumn('min_point', Table::TYPE_INTEGER, null, [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ], 'Min Point Value')
                    ->addColumn('sum_order', Table::TYPE_INTEGER, null, [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ], 'Number Of Order')
                    ->addColumn('description', Table::TYPE_TEXT, '64k', [], 'Description')
                    ->addColumn('earn_percent', Table::TYPE_INTEGER, null, [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ], 'Earn Point Percent')
                    ->addColumn('earn_fixed', Table::TYPE_INTEGER, null, [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ], 'Earn Point Fixed')
                    ->addColumn('spent_percent', Table::TYPE_INTEGER, null, [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ], 'Spent Percent')
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['default' => Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->addIndex(
                        $installer->getIdxName(
                            self::MAGEPLAZA_REWARD_MILESTONE,
                            ['tier_id']
                        ),
                        ['tier_id']
                    )
                    ->setComment('Mageplaza Reward Milestones');
                $connection->createTable($table);
            }

            /**
             * Mageplaza Reward milestone customer
             */
            if (!$installer->tableExists(self::MAGEPLAZA_REWARD_MILESTONE_CUSTOMER)) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable(self::MAGEPLAZA_REWARD_MILESTONE_CUSTOMER))
                    ->addColumn('customer_id', Table::TYPE_INTEGER, null, [
                        'unsigned' => true,
                        'primary' => true,
                        'nullable' => false
                    ], 'Customer ID')
                    ->addColumn('tier_id', Table::TYPE_INTEGER, null, [
                        'unsigned' => true,
                        'primary' => true,
                        'nullable' => false
                    ], 'Milestone Tier ID')
                    ->addIndex($installer->getIdxName(
                        self::MAGEPLAZA_REWARD_MILESTONE_CUSTOMER,
                        ['customer_id']
                    ), ['customer_id'])
                    ->addIndex($installer->getIdxName(
                        self::MAGEPLAZA_REWARD_MILESTONE_CUSTOMER,
                        ['tier_id']
                    ), ['tier_id'])
                    ->addForeignKey(
                        $installer->getFkName(
                            self::MAGEPLAZA_REWARD_MILESTONE_CUSTOMER,
                            'customer_id',
                            'customer_entity',
                            'entity_id'
                        ),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            self::MAGEPLAZA_REWARD_MILESTONE_CUSTOMER,
                            'tier_id',
                            self::MAGEPLAZA_REWARD_MILESTONE,
                            'tier_id'
                        ),
                        'tier_id',
                        $installer->getTable(self::MAGEPLAZA_REWARD_MILESTONE),
                        'tier_id',
                        Table::ACTION_CASCADE
                    )
                    ->addIndex(
                        $installer->getIdxName(
                            self::MAGEPLAZA_REWARD_MILESTONE_CUSTOMER,
                            ['customer_id', 'tier_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['customer_id', 'tier_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->setComment('Milestone To Customer Link Table');

                $connection->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            if ($installer->tableExists('mageplaza_reward_behavior')) {
                $connection->addColumn(
                    $installer->getTable('mageplaza_reward_behavior'),
                    'min_grand_total',
                    [
                        'type'    => Table::TYPE_DECIMAL,
                        'length'  => '10,2',
                        'comment' => 'Min Grand Total',
                        'after'   => 'min_words'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
