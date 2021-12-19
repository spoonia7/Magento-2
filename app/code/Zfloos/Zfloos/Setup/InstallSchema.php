<?php

namespace Zfloos\Zfloos\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('custom_order_quote')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('custom_order_quote')
			)
				->addColumn(
					'coq_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
					11,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'custom order quote id'
				)
				->addColumn(
					'quote_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
					11,
					[],
					'Quote ID'
				)
				->addColumn(
					'order_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
					11,
					[],
					'Order ID'
				)
				->setComment('custom order quote Table');
			$installer->getConnection()->createTable($table);

		}
		$installer->endSetup();
	}
}