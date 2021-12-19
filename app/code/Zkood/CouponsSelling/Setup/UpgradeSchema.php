<?php

namespace Zkood\CouponsSelling\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
/**
 * CouponSelling schema update
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        $version = $context->getVersion();
        $connection = $setup->getConnection();

        if (version_compare($version, '2.0.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('zkood_coupons_entity'),
                'product_name',
                ['type' => Table::TYPE_TEXT, 'nullable' => true, 'comment' => 'product name']
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('zkood_coupons_entity'),
                'product_price',
                ['type' => Table::TYPE_DECIMAL, 'nullable' => true, 'comment' => 'product price']
            );
        }

        $setup->endSetup();
    }
}
