<?php

namespace Zkood\CouponsSelling\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /*
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return null
    */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (!$setup->tableExists('zkood_coupons_entity')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('zkood_coupons_entity')
            )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Entity  ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                        'unsigned' => true,
                    ],
                    'Customer ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Seller ID'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Product ID'
                )
                ->addColumn(
                    'coupon_code',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'unsigned' => true],
                    'Coupon Code'
                )
                ->addColumn(
                    'is_redeemed',
                    Table::TYPE_BOOLEAN,
                    1,
                    ['nullable' => false, 'unsigned' => true],
                    'Is Redeemed'
                )
                ->addColumn(
                    'valid_to',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Valid To'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                );
            $setup->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}
