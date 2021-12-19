<?php

namespace Zkood\RecipesManagement\Setup;

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
        if (!$setup->tableExists('zkood_recipes_entity')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('zkood_recipes_entity')
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
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Customer ID'
                )
                ->addColumn(
                    'customer_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'unsigned' => true],
                    'Customer Name'
                )
                ->addColumn(
                    'customer_email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Customer Email'
                )
                ->addColumn(
                    'recipe_image',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Recipe Image'
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

        // add notes column version 2.0.0
        $recipesTable = $setup->getTable('zkood_recipes_entity');
        $setup->getConnection()->addColumn($recipesTable, 'notes', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Customer Notes',
        ]);

        $setup->endSetup();
    }
}
